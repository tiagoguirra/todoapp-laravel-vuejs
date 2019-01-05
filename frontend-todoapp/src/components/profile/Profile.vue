<template>
  <div class="flex flex-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <ul class="nav nav-tabs">
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
                <div class="form-group col-sm-12" :class="{ 'form-group--error': $v.profile.name.$error }">
                  <label >Name</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter name"
                    v-model.trim="$v.profile.name.$model"
                    @input="$v.profile.name.$touch()"
                  >
                  <div class="error" v-if="$v.profile.name.$error && !$v.profile.name.required">Name is required</div>
                </div>
                <div class="form-group  col-sm-12" :class="{ 'form-group--error': $v.profile.email.$error }">
                  <label >Email address</label>
                  <input
                    type="email"
                    class="form-control"
                    placeholder="Enter email"
                    v-model.trim="$v.profile.email.$model"
                    @input="$v.profile.email.$touch()"
                  >
                  <div class="error" v-if="$v.profile.email.$error && !$v.profile.email.required">Email is required</div>
                  <div class="error" v-if="$v.profile.email.$error && !$v.profile.email.email">Email invalid</div>
                </div>
                <div class="form-button">
                  <button type="submit" class="btn btn-primary btn-block">Change</button>
                </div>
              </form>
            </div>
            <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
              <form class="form-profile col-md-12 row" @submit.prevent="onSubmitPassword">
                <div class="form-group col-sm-12" :class="{ 'form-group--error': $v.password.old_password.$error }">
                  <label >Old password</label>
                  <input
                    type="password"
                    class="form-control"
                    placeholder="Old password"
                    v-model.trim="$v.password.old_password.$model"
                    @input="$v.password.old_password.$touch()"
                  >
                  <div class="error" v-if="$v.password.old_password.$error && !$v.password.old_password.required">Old password is required</div>
                  <div class="error" v-if="$v.password.old_password.$error && !$v.password.old_password.minLength">Old password must have at least {{ $v.password.old_password.$params.minLength.min }} letters.</div>
                </div>
                <div class="form-group col-sm-12"  :class="{ 'form-group--error': $v.password.password.$error }">
                  <label >Password</label>
                  <input
                    type="password"
                    class="form-control"
                    placeholder="Password"
                    v-model.trim="$v.password.password.$model"
                    @input="$v.password.password.$touch()"
                  >
                  <div class="error" v-if="$v.password.password.$error && !$v.password.password.required">Password is required</div>
                  <div class="error" v-if="$v.password.password.$error && !$v.password.password.minLength">Password must have at least {{ $v.password.password.$params.minLength.min }} letters.</div>
                </div>
                <div class="form-group col-sm-12" :class="{ 'form-group--error': $v.password.password_confirmation.$error }">
                  <label >Password confirm</label>
                  <input
                    type="password"
                    class="form-control"
                    placeholder="Password confirm"
                    v-model.trim="$v.password.password_confirmation.$model"
                    @input="$v.password.password_confirmation.$touch()"
                  >
                  <div class="error" v-if="$v.password.password_confirmation.$error && !$v.password.password_confirmation.required">Password confirm is required</div>
                  <div class="error" v-if="$v.password.password_confirmation.$error && !$v.password.password_confirmation.minLength">Password confirm must have at least {{ $v.password.password_confirmation.$params.minLength.min }} letters.</div>
                  <div class="error" v-if="$v.password.password_confirmation.$error && !$v.password.password_confirmation.sameAsPassword">Passwords must be identical.</div>
                </div>
                <div class="form-button">
                  <button type="submit" class="btn btn-primary btn-block">Change password</button>
                </div>
              </form>
            </div>
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
.form-button{
  width: 100%;
  padding: 10px 15px 5px 15px;
}
</style>