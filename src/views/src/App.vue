<template>
  <div id="app">
    <div class="uk-width-1-2 uk-align-center">
        <div class="uk-column-1-1">
        <h1>Users</h1>
        <button @click="create" class="uk-button uk-button-primary">
            <span uk-icon="plus"></span> New user
        </button>
        </div>
        <table class="uk-table uk-table-divider">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-mail</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody v-if="data.length">
                <tr v-bind:key="item.userId" v-for="(item, index) in data">
                    <td>{{ item.userId }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.email }}</td>
                    <td>
                        <span @click="edit(item.userId, index)">
                            <span uk-icon="file-edit"></span>
                        </span>
                        <span @click="remove(item.userId, index)">
                            <span uk-icon="trash"></span>
                        </span>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="4">Nothing yet</td>
                </tr>
            </tbody>
        </table>
        <div id="my-id" uk-modal>
            <div class="uk-modal-dialog">
                <button class="uk-modal-close-default" type="button" uk-close></button>
                <div class="uk-modal-header">
                    <h2 class="uk-modal-title">User {{ user.userId }}</h2>
                </div>
                <div class="uk-modal-body">
                    <fieldset class="uk-fieldset">
                        <legend class="uk-legend">Name</legend>
                        <div class="uk-margin">
                            <input v-model="user.name" required class="uk-input" type="text" placeholder="User name">
                        </div>
                        <legend class="uk-legend">E-mail</legend>
                        <div class="uk-margin">
                            <input v-model="user.email" class="uk-input" required type="email" placeholder="User e-mail">
                        </div>
                    </fieldset>
                </div>
                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                    <button @click="saveAction" class="uk-button uk-button-primary" type="button">Save</button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import UIkit from 'uikit';
import 'uikit/dist/css/uikit.min.css';
import 'uikit/dist/js/uikit.min.js';
import Icons from 'uikit/dist/js/uikit-icons'
UIkit.use(Icons);

export default {
    name: "app",
    data: function() {
        return {
            data: [],
            user: {},
            userApiUrl: "http://localhost:8080/users"
        };
    },
    created: function () {
        this.$http.get(this.userApiUrl).then(
            response => {
                this.data = response.body;
            },
            response => {
            }
        )
    },
    methods: {
        remove: function(userId, index) {
            var self = this;
            UIkit.modal.confirm('Do you really wanna remove user ' + userId + '?').then(function() {
                self.removeAction(userId, index);
            }, function () {
            });
        },
        edit: function(userId, index) {
            UIkit.modal('#my-id').show();
            this.user = this.data[index];
        },
        create: function() {
            UIkit.modal('#my-id').show();
            this.user = {};
        },
        removeAction: function(userId, index) {
            this.$http.delete(this.userApiUrl + "/" + userId).then(
                response => {
                    this.data.splice(index, 1);
                },
                response => {
                    UIkit.modal.alert('Error trying to remove user ' + userId)
                }
            )
        },
        saveAction: function() {
            let user = this.user;
            if (! user.name) {
                alert('User name is required');
                return false;
            }
            if (! user.email) {
                alert('User e-mail is required');
                return false;
            }
            let httpMethod = 'post';
            if (user.userId) {
                httpMethod = 'put';
            }
            this.$http[httpMethod](this.userApiUrl + ((user.userId) ? ("/" + user.userId) : ""), {
                name: user.name,
                email: user.email
            }).then(
                response => {
                    user.userId = response.body.userId;
                    if (httpMethod == 'post') {
                        this.data.push(user);
                        this.data.sort(function(a, b) {
                            if (a.userId < b.userId) {
                                return 1;
                            }
                        });
                    }
                    UIkit.modal('#my-id').hide();
                },
                response => {}
            )
        }
    },
};
</script>

<style>
#app {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin-top: 60px;
}

td span {
    cursor: pointer;
    margin-right: 5px;
}
</style>
