<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">TodoAPP</a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse flex flex-content-end" id="navbarSupportedContent">
        <ul class="navbar-nav">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item dropdown" v-if="isAuthenticated">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="navbarDropdown"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >{{getUserProfile.name}}</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <router-link to="/profile" class="dropdown-item">Profile</router-link>
              <a class="dropdown-item" @click.prevent="logout">Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script>
import { AUTH_LOGOUT } from "../../store/actions/auth";
import { mapGetters } from "vuex";
export default {
  name: "Menu",
  computed: {
    ...mapGetters(["isAuthenticated", "getUserProfile"])
  },
  methods: {
    logout: function() {
      this.$store.dispatch(AUTH_LOGOUT).then(() => this.$router.push("/login"));
    }
  }
};
</script>

<style>
@media (max-width: 992px) {
  .navbar-collapse.flex.flex-content-end.collapse.show {
    justify-content: center;
  }
}
</style>