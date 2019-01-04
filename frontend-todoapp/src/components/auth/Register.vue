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
              <label for="exampleInputEmail1">Name</label>
              <input
                type="text"
                class="form-control"                
                placeholder="Enter name"
                v-model.trim="$v.form.name.$model"
              >
            </div>
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
            <div class="form-group">
              <label for="exampleInputPassword1">Password confirm</label>
              <input
                type="password"
                class="form-control"                
                placeholder="Password confirm"
                v-model.trim="$v.form.password_confirmation.$model"
              >
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
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
</style>