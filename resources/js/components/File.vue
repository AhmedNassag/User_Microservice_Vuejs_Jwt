<template>
    <div class="container">
        <!-- Add New File Button -->
        <button
            v-if="token"
            type="button"
            class="btn btn-success float-right m-1"
            data-toggle="modal"
            data-target="#fileModal">
            Add New File
        </button>

        <!-- File Modal -->
        <div
            class="modal fade"
            id="fileModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="fileModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileModalLabel">
                            {{ edit ? "Edit File" : "Add New File" }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- File Input -->
                        <input
                            type="file"
                            :class="['form-control my-2', errors.file ? 'is-invalid' : '']"
                            @change="handleFileUpload">
                        <span
                            class="bg-danger text-white p-1 rounded"
                            v-if="errors.file">
                            {{ errors.file[0] }}
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-danger"
                            data-dismiss="modal">
                            Close
                        </button>
                        <button
                            v-if="edit"
                            type="button"
                            class="btn btn-success"
                            @click="updateFile">
                            Update
                        </button>
                        <button
                            v-else
                            type="button"
                            class="btn btn-success"
                            @click="storeFile">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- File List -->
        <div class="my-3" v-for="file in files" :key="file.id">
            <h5>{{ file.name }}</h5>
            <img :src="file.url" width="100" height="100">
            <a :href="file.url" target="_blank" class="btn btn-info btn-sm">View</a>
            <button
                v-if="token"
                type="button"
                class="btn btn-primary btn-sm"
                @click="editFile(file)"
                data-toggle="modal"
                data-target="#fileModal">
                Edit
            </button>
            <button
                v-if="token"
                type="button"
                class="btn btn-danger btn-sm"
                @click="deleteFile(file.id)">
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
            files: [], // Array to hold file data
            edit: false, // Flag to determine add or edit mode
            fileData: {
                id: null,
                file: null,
            },
            errors: [],
            token: null, // Authentication token
        };
    },
    created() {
        this.checkToken();
        this.fetchFiles();
    },
    methods: {
        // Check if token exists
        checkToken() {
            this.token = localStorage.getItem("token");
        },
        // Fetch files from API
        fetchFiles() {
            let user = JSON.parse(localStorage.getItem("user"));
            axios
                .get("http://127.0.0.1:8001/api/files?mediable_type=User&mediable_id="+user.id, {
                    headers: { Authorization: `Bearer ${this.token}` },
                })
                .then((response) => {
                    this.files = response.data.data;
                });
        },
        // Handle file input change
        handleFileUpload(event) {
            this.fileData.file = event.target.files[0];
        },
        // Store a new file
        storeFile() {
            const formData = new FormData();
            formData.append("file", this.fileData.file);
            formData.append("mediable_type", "User"); // Set mediable_type to "User"

            // Add user_id dynamically from localStorage
            const user = JSON.parse(localStorage.getItem("user"));
            if (user && user.id) {
                formData.append("mediable_id", user.id);
            } else {
                Swal.fire("Error", "User is not authenticated", "error");
                return;
            }

            axios
                .post("http://127.0.0.1:8001/api/files", formData, {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.files.push(response.data.file);
                    this.resetFileData();
                    $("#fileModal").modal("hide");
                    Swal.fire("Success", "File added successfully", "success");
                })
                .catch((error) => {
                    this.errors = error.response.data.errors || [];
                });
        },
        // Edit a file
        editFile(file) {
            this.fileData.id = file.id;
            this.edit = true;
        },
        // Update an existing file
        updateFile() {
            const formData = new FormData();
            formData.append("file", this.fileData.file);

            axios
                .post(`http://127.0.0.1:8001/api/files/${this.fileData.id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    const index = this.files.findIndex(
                        (file) => file.id === this.fileData.id
                    );
                    this.$set(this.files, index, response.data.file);
                    this.resetFileData();
                    $("#fileModal").modal("hide");
                    Swal.fire("Success", "File updated successfully", "success");
                })
                .catch((error) => {
                    this.errors = error.response.data.errors || [];
                });
        },
        // Delete a file
        deleteFile(id) {
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
                        .delete(`http://127.0.0.1:8001/api/files/${id}`, {
                            headers: { Authorization: `Bearer ${this.token}` },
                        })
                        .then(() => {
                            this.files = this.files.filter((file) => file.id !== id);
                            Swal.fire("Deleted!", "Your file has been deleted.", "success");
                        });
                }
            });
        },
        // Reset file data
        resetFileData() {
            this.fileData = {
                id: null,
                file: null,
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
