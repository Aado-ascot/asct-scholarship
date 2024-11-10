<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white q-pt-sm q-pb-sm q-pl-xl q-pr-xl">
      <q-toolbar>
        <q-toolbar-title>
          <img class="q-mt-xs" width="20%" src="/imgs/ASCT_logo2.png">
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="q-pl-xl q-pr-xl q-pt-md">
        <router-view />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { LocalStorage } from 'quasar'
import login from '../firebase/firebase-login'
import getDetailsDocument from '../firebase/firebase-get';

export default {
  name: 'MainLayout',
  data: () => {
    return {
      keepLogin: false,
      loginLoad: false,
      form: {
        username: "",
        password: "",
      },
      formRules: {
        username: {
          rules: [
            value => !!value || 'This field must be filled!',
          ]
        },
        password: {
          rules: [
            val => !!val || 'This field must be filled!',
          ]
        },
      },
    }
  },
  methods: {
    async submitLogin(){
      this.loginLoad = true;
      let vm = this;
      let payload = {
        email: vm.form.username,
        password: vm.form.password
      };

      await login(payload).then(async (res) => {
        let id = res.uid
        const user = await getDetailsDocument(`userProfile`, id)
        LocalStorage.set('userData', user);

        this.loginLoad = false;
        this.$router.push('user/dashboard')
      })
    },
    async submitLoginFB(){
      this.$q.loading.show();
      this.loginLoad = true;
      let vm = this;
      let payload = vm.form;

      this.$api.post('auth/login', payload).then(async (response) => {
        const data = {...response.data};
        if(!data.error){
          await LocalStorage.set('userData', data.jwt);
          this.$router.push('user/dashboard')
        } else {
          this.$q.notify({
            position: 'top-left',
            type: 'negative',
            message: data.title,
            caption: data.message,
            icon: 'report_problem'
          })
        }
      })

      this.loginLoad = false;
      this.$q.loading.hide();
    }
  }
}
</script>

<style scoped>
.linkCustom {
  text-decoration: none;
}
.btn-custom-border{
  border-radius: 10px;
}
.activeMenu {
  border-radius: 50% !important;
  padding: 10%;
  background-color: bisque;
}
</style>