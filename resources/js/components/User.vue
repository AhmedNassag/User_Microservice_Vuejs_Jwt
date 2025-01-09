<template>
    <div class="container">
        <!-- Add New User Button -->
        <button v-if="token" type="button" class="btn btn-success float-right m-1" data-toggle="modal"
            data-target="#userModal" @click="openModal">
            Add New User
        </button>

        <!-- User Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalLabel">
                            {{ edit ? "Edit User" : "Add New User" }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- User Input -->
                        <input type="text" v-model="userData.name"
                            :class="['form-control my-2', errors.name ? 'is-invalid' : '']"
                            placeholder="Enter user name">
                        <span class="bg-danger text-white p-1 rounded" v-if="errors.name">
                            {{ errors.name[0] }}
                        </span>

                        <input type="email" v-model="userData.email"
                            :class="['form-control my-2', errors.email ? 'is-invalid' : '']"
                            placeholder="Enter user email">
                        <span class="bg-danger text-white p-1 rounded" v-if="errors.email">
                            {{ errors.email[0] }}
                        </span>

                        <input type="password" v-model="userData.password"
                            :class="['form-control my-2', errors.password ? 'is-invalid' : '']"
                            placeholder="Enter user password">
                        <span class="bg-danger text-white p-1 rounded" v-if="errors.password">
                            {{ errors.password[0] }}
                        </span>


                        <!-- Roles List -->
                        <div class="row">
                            <div
                                class="col mb-3 d-flex"
                                v-for="role in roles"
                                :key="role._id"
                            >
                                <div class="card flex-fill">
                                    <div class="card-body p-3 text-center">
                                        <p class="card-text f-12">{{ role }}</p>
                                    </div>
                                    <div class="card-footer p-3 text-center">
                                        <label class="form-group toggle-switch mb-0">
                                            <input
                                                type="checkbox"
                                                class="toggle-switch-input"
                                                v-model="userData.roles"
                                                :value="role"
                                            />
                                            <span class="toggle-switch-label mx-auto">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal" @click="resetUserData">
                            Close
                        </button>
                        <button v-if="edit" type="button" class="btn btn-success" @click="updateUser">
                            Update
                        </button>
                        <button v-else type="button" class="btn btn-success" @click="storeUser">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- User List -->
        <div class="my-3" v-for="user in users" :key="user._id">
            <h5>{{ user.name }}</h5>
            <h5>{{ user.email }}</h5>
            <button v-if="token" type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                data-target="#userModal" @click="editUser(user)">
                Edit
            </button>
            <button v-if="token" type="button" class="btn btn-danger btn-sm" @click="deleteUser(user._id)">
                Delete
            </button>
            <hr>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import Swal from "sweetalert2";

    export default {
        data() {
            return {
                users: [], // Array to hold users
                roles: [], // Array to hold roles
                edit: false, // Flag to determine add or edit mode
                userData: {
                    id: null,
                    name: "",
                    email: "",
                    password: "",
                    roles: [], // Selected roles
                },
                errors: [],
                token: null, // Authentication token
            };
        },
        created() {
            this.checkToken();
            this.fetchUsers();
        },
        methods: {
            openModal() {
                this.edit = false;
                this.resetUserData();
                this.fetchRoles();
            },
            closeModal() {
                this.resetUserData();
            },
            // Check if token exists
            checkToken() {
                this.token = localStorage.getItem("token");
            },
            // Fetch users from API
            fetchUsers() {
                axios
                    .get("api/users", {
                        headers: { Authorization: `Bearer ${this.token}` },
                    })
                    .then((response) => {
                        this.users = response.data.data.users;
                    });
            },
            // Fetch roles from API
            fetchRoles() {
                if (this.roles.length === 0) { // Only fetch if not already cached
                    axios
                        .get("api/users", {
                            headers: { Authorization: `Bearer ${this.token}` },
                        })
                        .then((response) => {
                            this.roles = response.data.data.roles;
                        })
                        .catch((error) => {
                            console.error("Error fetching roles:", error);
                        });
                }
            },
            // Store a new user
            storeUser() {
                axios
                    .post(
                        "api/users",
                        {
                            name: this.userData.name,
                            email: this.userData.email,
                            password: this.userData.password,
                            roles: this.userData.roles
                        },
                        {
                            headers: { Authorization: `Bearer ${this.token}` },
                        }
                    )
                    .then((response) => {
                        this.users.push(response.data.user);
                        this.resetUserData();
                        this.closeModal();
                        this.fetchUsers();
                        Swal.fire("Success", "User added successfully", "success");
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                    });
            },
            // Edit a user
            editUser(user) {
                this.userData.id   = user._id;
                this.userData.name = user.name;
                this.userData.email = user.email;
                this.userData.password = user.password;
                // this.userData.roles = [];
                // this.userData.roles = user.roles.map((role) => role.name) || []; // Assuming roles are objects
                this.userData.roles = (user.roles || []).map((rol) => rol.name);
                this.edit          = true;
                this.fetchRoles(); // Fetch roles when editing
            },
            // Update an existing role
            updateUser() {
                axios
                    .put(
                        `api/users/${this.userData.id}`,
                        {
                            name: this.userData.name,
                            email: this.userData.email,
                            password: this.userData.password,
                            roles: this.userData.roles,
                        },
                        {
                            headers: { Authorization: `Bearer ${this.token}` },
                        }
                    )
                    .then((response) => {
                        const index = this.users.findIndex(
                            (user) => user._id === this.userData.id
                        );
                        this.$set(this.users, index, response.data.user);
                        this.resetUserData();
                        this.closeModal();
                        this.fetchUsers();
                        Swal.fire("Success", "User updated successfully", "success");
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                    });
            },
            // Delete a user
            deleteUser(id) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios
                            .delete(`api/users/${id}`, {
                                headers: { Authorization: `Bearer ${this.token}` },
                            })
                            .then(() => {
                                this.users = this.users.filter((user) => user._id !== id);
                                this.fetchUsers();
                                Swal.fire("Deleted!", "Your user has been deleted.", "success");
                            });
                    }
                });
            },
            // Reset user data
            resetUserData() {
                this.userData = {
                    id: null,
                    name: "",
                    email: "",
                    password: "",
                    roles: [],
                };
                this.edit   = false;
                this.errors = [];
                $('#roleModal').modal('hide');
            },
        },
    };
</script>

<style scoped>
    .container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
    }
</style>
