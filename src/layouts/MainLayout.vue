<template>
  <q-layout view="lHh Lpr lFf">
    <!-- <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      width="500"
    >
      <div class="fit row wrap justify-center items-center content-center q-pa-xl">
        <div class="col-12 text-bold text-h5 q-mb-xl text-orange">
          <q-img src="/imgs/bagamalogo.jpg" />
        </div>
        <div class="col-12 text-bold text-h5 q-mt-xl">Log in</div>
        <div class="col-12 text-grey-8">Trusted authorized aircon distributor providing complete sales, installation, and repair services.</div>
        
        <div class="col-12 text-grey-8 q-mt-lg">
          <q-input
            v-model="form.username"
            @keypress.enter="submitLogin"
            label="Username/Email"
            v-bind="formRules.username"
            placeholder="Enter username / email"
            outlined  
            stack-label
          >
            <template v-slot:prepend>
              <q-icon name="ti-user" />
            </template>
          </q-input>
        </div>
        <div class="col-12 text-grey-8 q-mt-sm">
          <q-input 
            v-model="form.password"
            v-bind="formRules.password"
            @keypress.enter="submitLogin"
            type="password"
            label="Password"
            placeholder="***********"
            outlined  
            stack-label
          >
            <template v-slot:prepend>
              <q-icon name="ti-lock" />
            </template>
          </q-input>
        </div>
        <div class="col-12 q-mt-sm">
          <q-checkbox v-model="keepLogin" label="Remember me" />
          <q-btn class="float-right" flat color="orange" no-caps label="Forgot Password" />
        </div>
        <div class="col-12 q-mt-lg">
          <q-btn
            type="submit"
            class="full-width q-pa-md btn-custom-border" 
            unelevated
            :loading="loginLoad"
            no-caps 
            color="primary" 
            label="Let's Start"
            @click="submitLogin"
          />
        </div>
      </div>
    </q-drawer> -->
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