<template>
  <div>
    <div class="card">
      <div class="card-body">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a
              class="nav-link active"
              id="profile-tab"
              data-toggle="tab"
              href="#profile"
              role="tab"
              aria-controls="profile"
              aria-selected="true"
            >Data</a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              id="password-tab"
              data-toggle="tab"
              href="#password"
              role="tab"
              aria-controls="password"
              aria-selected="true"
            >Password</a>
          </li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div
            class="tab-pane fade show active"
            id="profile"
            role="tabpanel"
            aria-labelledby="profile-tab"
          >
            <form class="form-profile col-md-12 row" @submit.prevent="onSubmitProfile">
              <div class="form-group col-md-6 col-sm-12">
                <label for="exampleInputEmail1">Name</label>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Enter name"
                  v-model.trim="$v.profile.name.$model"
                >
              </div>
              <div class="form-group col-md-6 col-sm-12">
                <label for="exampleInputEmail1">Email address</label>
                <input
                  type="email"
                  class="form-control"
                  placeholder="Enter email"
                  v-model.trim="$v.profile.email.$model"
                >
              </div>
              <button type="submit" class="btn btn-primary">Change</button>
            </form>
          </div>
          <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
            <form class="form-profile col-md-12 row" @submit.prevent="onSubmitPassword">
              <div class="form-group col-md-4 col-sm-12">
                <label for="exampleInputPassword1">Old password</label>
                <input
                  type="password"
                  class="form-control"
                  placeholder="Old password"
                  v-model.trim="$v.password.old_password.$model"
                >
              </div>
              <div class="form-group col-md-4 col-sm-12">
                <label for="exampleInputPassword1">Password</label>
                <input
                  type="password"
                  class="form-control"
                  placeholder="Password"
                  v-model.trim="$v.password.password.$model"
                >
              </div>
              <div class="form-group col-md-4 col-sm-12">
                <label for="exampleInputPassword1">Password confirm</label>
                <input
                  type="password"
                  class="form-control"
                  placeholder="Password confirm"
                  v-model.trim="$v.password.password_confirmation.$model"
                >
              </div>
              <button type="submit" class="btn btn-primary">Change password</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { email, required, minLength, sameAs } from "vuelidate/lib/validators";
import {
  USER_REQUEST,
  USER_UPDATE,
  USER_UPDATE_PASS
} from "@/store/actions/user";

export default {
  name: "Profile",
  data: () => ({
    profile: {
      name: "",
      email: ""
    },
    password: {
      old_password: "",
      password: "",
      password_confirmation: ""
    }
  }),
  validations: {
    profile: {
      name: {
        required
      },
      email: {
        required,
        email
      }
    },
    password: {
      old_password: {
        required,
        minLength: minLength(6)
      },
      password: {
        required,
        minLength: minLength(6)
      },
      password_confirmation: {
        required,
        minLength: minLength(6),
        sameAsPassword: sameAs("password")
      }
    },
    validationGroup: ["profile", "password"]
  },
  methods: {
    getData() {
      this.$store.dispatch(USER_REQUEST).then(resp => {
        this.profile = { ...resp.data.user };
      });
    },
    onSubmitProfile() {
      this.$v.profile.$touch();
      if (!this.$v.profile.$invalid) {
        this.$store.dispatch(USER_UPDATE, this.profile).then(resp => {
          this.$vueOnToast.pop("success", "User updated");
          this.getData();
        });
      } else {
        console.log("Is invalid");
      }
    },
    onSubmitPassword() {
      this.$v.password.$touch();
      if (!this.$v.password.$invalid) {
        this.$store.dispatch(USER_UPDATE_PASS, this.password).then(resp => {
          this.$vueOnToast.pop("success", "Password updated");
        });
      } else {
        console.log("Is invalid");
      }
    }
  },
  created() {
    this.getData();
  }
};
</script>

<style>
.form-profile {
  padding: 15px;
}
</style>