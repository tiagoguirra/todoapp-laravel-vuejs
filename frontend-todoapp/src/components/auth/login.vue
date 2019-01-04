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
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input
                type="email"
                class="form-control"                
                placeholder="Enter email"
                v-model.trim="$v.form.email.$model"
              >
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input
                type="password"
                class="form-control"                
                placeholder="Password"
                v-model.trim="$v.form.password.$model"
              >
            </div>
            <small>Don't have account? <router-link to="/register" class="register-now">REGISTER NOW</router-link></small>            
            <br>
            <br>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { email, required, minLength } from "vuelidate/lib/validators";
import {AUTH_REQUEST} from '@/store/actions/auth'

export default {
  name: "Login",
  data: () => ({
    form: {
      email: "",
      password: ""
    }
  }),
  validations: {
    form: {
      email: {
        required,
        email
      },
      password: {
        required,
        minLength: minLength(6)
      }
    }
  },
  methods: {
    onSubmit() {
      this.$v.form.$touch();
      if (!this.$v.form.$invalid) {
          console.log("Is valid");
          this.$store.dispatch(AUTH_REQUEST,this.form)
          .then(resp=>{
            this.$vueOnToast.pop('success','Authorized')
            this.$router.replace("/");
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
.register-now{
  outline: none;  
  color:#007bff;
}
.register-now:hover{
  text-decoration: none;  
}
</style>