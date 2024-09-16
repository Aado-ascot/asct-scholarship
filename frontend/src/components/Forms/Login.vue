<template>
    <div class="fit row wrap justify-center items-center content-center q-pa-xl">
        <div class="col-12 text-grey-8">Embrace the new year with a fresh opportunity!</div>

        <div class="col-12 text-grey-8 q-mt-lg">
            <q-input
                v-model="form.username"
                @keypress.enter="submitLogin"
                label="Username/Student Number"
                v-bind="formRules.username"
                placeholder="Enter username / student number"
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
                label="Login"
                @click="submitLogin"
            />
        </div>
    </div>
</template>

<script>
import { LocalStorage } from 'quasar'

export default {
  name:"LoginComponent",
  data() {
    return {
      tab: "login",
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
