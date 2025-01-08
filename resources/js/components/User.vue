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
                        <h5 class="modal-title" id="userModalLabel">
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
        <div class="my-3" v-for="user in users" :key="user.id">
            <h5>{{ user.name }}</h5>
            <button v-if="token" type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                data-target="#userModal" @click="editUser(user)">
                Edit
            </button>
            <button v-if="token" type="button" class="btn btn-danger btn-sm" @click="deleteUser(user.id)">
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
            edit: false, // Flag to determine add or edit mode
            userData: {
                id: null,
                name: "",
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
                    this.users = response.data.data;
                });
        },
        // Store a new user
        storeUser() {
            axios
                .post(
                    "api/users",
                    { name: this.userData.name },
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
            this.userData.id = user.id;
            this.userData.name = user.name;
            this.edit = true;
        },
        // Update an existing user
        updateUser() {
            axios
                .put(
                    `api/users/${this.userData.id}`,
                    { name: this.userData.name },
                    {
                        headers: { Authorization: `Bearer ${this.token}` },
                    }
                )
                .then((response) => {
                    const index = this.users.findIndex(
                        (user) => user.id === this.userData.id
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
                            this.users = this.users.filter((user) => user.id !== id);
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
            };
            this.edit = false;
            this.errors = [];
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
