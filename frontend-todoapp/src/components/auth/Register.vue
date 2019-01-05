<template>
  <div class="content-login">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="auth-title">
            <h3>TodoApp</h3>
            <h4>Login</h4>
          </div>
          <form class="form-login" @submit.prevent="onSubmit">
            <div class="form-group" :class="{ 'form-group--error': $v.form.name.$error }">
              <label>Name</label>
              <input
                type="text"
                class="form-control"                
                placeholder="Enter name"
                v-model.trim="$v.form.name.$model"
                @input="$v.form.name.$touch()"
              >
              <div class="error" v-if="$v.form.name.$error && !$v.form.name.required">Name is required</div>
            </div>
            <div class="form-group" :class="{ 'form-group--error': $v.form.email.$error }">
              <label>Email address</label>
              <input
                type="email"
                class="form-control"                
                placeholder="Enter email"
                v-model.trim="$v.form.email.$model"
                @input="$v.form.email.$touch()"
              >
              <div class="error" v-if="$v.form.email.$error && !$v.form.email.required">Email is required</div>
              <div class="error" v-if="$v.form.email.$error && !$v.form.email.email">Email invalid</div>
            </div>
            <div class="form-group" :class="{ 'form-group--error': $v.form.password.$error }">
              <label>Password</label>
              <input
                type="password"
                class="form-control"                
                placeholder="Password"
                v-model.trim="$v.form.password.$model"
                @input="$v.form.password.$touch()"
              >
              <div class="error" v-if="$v.form.password.$error && !$v.form.password.required">Password is required</div>
              <div class="error" v-if="$v.form.password.$error && !$v.form.password.minLength">Password must have at least {{ $v.form.password.$params.minLength.min }} letters.</div>
            </div>
            <div class="form-group" :class="{ 'form-group--error': $v.form.password_confirmation.$error }">
              <label>Password confirm</label>
              <input
                type="password"
                class="form-control"                
                placeholder="Password confirm"
                v-model.trim="$v.form.password_confirmation.$model"
                @input="$v.form.password_confirmation.$touch()"
              >
              <div class="error" v-if="$v.form.password_confirmation.$error && !$v.form.password_confirmation.required">Password confirm is required</div>
              <div class="error" v-if="$v.form.password_confirmation.$error && !$v.form.password_confirmation.minLength">Password confirm must have at least {{ $v.form.password_confirmation.$params.minLength.min }} letters.</div>
              <div class="error" v-if="$v.form.password_confirmation.$error && !$v.form.password_confirmation.sameAsPassword">Passwords must be identical.</div>
            </div>            
            <small>Already have an account? <router-link to="/login" class="login-now">LOGIN NOW</router-link></small>            
            <br>
            <br>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { email, required, minLength,sameAs } from "vuelidate/lib/validators";
import {AUTH_REGISTER} from '@/store/actions/auth'

export default {
  name: "Login",
  data: () => ({
    form: {
      name:"",
      email: "",
      password: "",
      password_confirmation:""
    }
  }),
  validations: {
    form: {
      name:{
        required
      },
      email: {
        required,
        email
      },
      password: {
        required,
        minLength: minLength(6)
      },
      password_confirmation:{
        required,
        minLength: minLength(6),
        sameAsPassword: sameAs("password")
      }
    }
  },
  methods: {
    onSubmit() {
      this.$v.form.$touch();
      if (!this.$v.form.$invalid) {
          console.log("Is valid");
          this.$store.dispatch(AUTH_REGISTER,this.form)
          .then(resp=>{
            this.$vueOnToast.pop('success','Registrad')
            this.$router.replace("/login");
          })
      }else{
          console.log('Invalid form')
      }
    }
  }
};
</script>

<style>
.content-login {
  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 60px;
}
.content-login .card {
  border-radius: 15px;
}
.auth-title {
  text-align: center;
  padding-bottom: 15px;
}
.form-login {
  padding: 15px;
}
.login-now{
  outline: none;  
  color:#007bff;
}
.login-now:hover{
  text-decoration: none;  
}
</style>