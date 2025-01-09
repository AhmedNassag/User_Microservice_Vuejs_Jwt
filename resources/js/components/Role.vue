<template>
    <div class="container">
        <!-- Add New Role Button -->
        <button v-if="token" type="button" class="btn btn-success float-right m-1" data-toggle="modal"
            data-target="#roleModal" @click="openModal">
            Add New Role
        </button>

        <!-- Role Modal -->
        <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalLabel">
                            {{ edit ? "Edit Role" : "Add New Role" }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Role Input -->
                        <input type="text" v-model="roleData.name"
                            :class="['form-control my-2', errors.name ? 'is-invalid' : '']"
                            placeholder="Enter role name">
                        <span class="bg-danger text-white p-1 rounded" v-if="errors.name">
                            {{ errors.name[0] }}
                        </span>


                        <!-- Permissions List -->
                        <div class="row">
                            <div
                                class="col mb-3 d-flex"
                                v-for="permission in permissions"
                                :key="permission._id"
                            >
                                <div class="card flex-fill">
                                    <div class="card-body p-3 text-center">
                                        <p class="card-text f-12">{{ permission }}</p>
                                    </div>
                                    <div class="card-footer p-3 text-center">
                                        <label class="form-group toggle-switch mb-0">
                                            <input
                                                type="checkbox"
                                                class="toggle-switch-input"
                                                v-model="roleData.permissions"
                                                :value="permission"
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
                        <button type="button" class="btn btn-danger" data-dismiss="modal" @click="resetRoleData">
                            Close
                        </button>
                        <button v-if="edit" type="button" class="btn btn-success" @click="updateRole">
                            Update
                        </button>
                        <button v-else type="button" class="btn btn-success" @click="storeRole">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role List -->
        <div class="my-3" v-for="role in roles" :key="role._id">
            <h5>{{ role.name }}</h5>
            <button v-if="token" type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                data-target="#roleModal" @click="editRole(role)">
                Edit
            </button>
            <button v-if="token" type="button" class="btn btn-danger btn-sm" @click="deleteRole(role._id)">
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
                roles: [], // Array to hold roles
                permissions: [], // Array to hold permissions
                edit: false, // Flag to determine add or edit mode
                roleData: {
                    id: null,
                    name: "",
                    permissions: [], // Selected permissions
                },
                errors: [],
                token: null, // Authentication token
            };
        },
        created() {
            this.checkToken();
            this.fetchRoles();
        },
        methods: {
            openModal() {
                this.edit = false;
                this.resetRoleData();
                this.fetchPermissions();
            },
            closeModal() {
                this.resetRoleData();
            },
            // Check if token exists
            checkToken() {
                this.token = localStorage.getItem("token");
            },
            // Fetch roles from API
            fetchRoles() {
                axios
                    .get("api/roles", {
                        headers: { Authorization: `Bearer ${this.token}` },
                    })
                    .then((response) => {
                        this.roles = response.data.data.roles;
                    });
            },
            // Fetch permissions from API
            fetchPermissions() {
                if (this.permissions.length === 0) { // Only fetch if not already cached
                    axios
                        .get("api/roles", {
                            headers: { Authorization: `Bearer ${this.token}` },
                        })
                        .then((response) => {
                            this.permissions = response.data.data.permissions;
                        })
                        .catch((error) => {
                            console.error("Error fetching permissions:", error);
                        });
                }
            },
            // Store a new role
            storeRole() {
                axios
                    .post(
                        "api/roles",
                        {
                            name: this.roleData.name,
                            permissions: this.roleData.permissions
                        },
                        {
                            headers: { Authorization: `Bearer ${this.token}` },
                        }
                    )
                    .then((response) => {
                        this.roles.push(response.data.role);
                        this.resetRoleData();
                        this.closeModal();
                        this.fetchRoles();
                        Swal.fire("Success", "Role added successfully", "success");
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                    });
            },
            // Edit a role
            editRole(role) {
                this.roleData.id   = role._id;
                this.roleData.name = role.name;
                // this.roleData.permissions = role.permissions || [];
                // this.roleData.permissions = role.permissions.map((perm) => perm.name) || []; // Assuming permissions are objects
                this.roleData.permissions = (role.permissions || []).map((perm) => perm.name);
                this.edit          = true;
                this.fetchPermissions(); // Fetch permissions when editing
            },
            // Update an existing role
            updateRole() {
                axios
                    .put(
                        `api/roles/${this.roleData.id}`,
                        {
                            name: this.roleData.name,
                            permissions: this.roleData.permissions,
                        },
                        {
                            headers: { Authorization: `Bearer ${this.token}` },
                        }
                    )
                    .then((response) => {
                        const index = this.roles.findIndex(
                            (role) => role._id === this.roleData.id
                        );
                        this.$set(this.roles, index, response.data.role);
                        this.resetRoleData();
                        this.closeModal();
                        this.fetchRoles();
                        Swal.fire("Success", "Role updated successfully", "success");
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                    });
            },
            // Delete a role
            deleteRole(id) {
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
                            .delete(`api/roles/${id}`, {
                                headers: { Authorization: `Bearer ${this.token}` },
                            })
                            .then(() => {
                                this.roles = this.roles.filter((role) => role._id !== id);
                                this.fetchRoles();
                                Swal.fire("Deleted!", "Your role has been deleted.", "success");
                            });
                    }
                });
            },
            // Reset role data
            resetRoleData() {
                this.roleData = {
                    id: null,
                    name: "",
                    permissions: [],
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
