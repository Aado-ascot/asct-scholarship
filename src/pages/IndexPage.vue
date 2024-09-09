<template>
  <div class="row">
    <div class="col-12 col-md-7 q-pa-sm">
      <q-card
        flat
        class="my-card bg-white"
      >
        <q-card-section>
          <span class="text-h6 text-bold">ASCT Scholarship Services</span><br/>
          <div class="row">
            <div
              v-for="(item, idx) in services"
              :key="idx"
              class="col-12 col-md-4 q-pa-lg"
            >
              <q-card class="my-card" flat bordered>
                <q-img src="/imgs/ASCOT-WEBSITE.png" />

                <q-card-section>
                  <q-btn
                    fab
                    color="primary"
                    :icon="item.icon"
                    class="absolute"
                    style="top: 0; right: 12px; transform: translateY(-50%);"
                  />

                  <div class="row no-wrap items-center">
                    <div class="col text-h6 ellipsis">
                      {{item.title}}
                    </div>
                  </div>
                </q-card-section>

                <q-card-section class="q-pt-none">
                  <div class="text-caption text-grey">
                    {{item.description}}
                  </div>
                </q-card-section>

                <q-separator />

                <q-card-actions>
                  <q-btn flat color="primary">
                    Reserve
                  </q-btn>
                </q-card-actions>
              </q-card>
            </div>
          </div>

          <q-separator />
          <span class="text-h6 text-bold">Opportunities</span><br/>
          <q-img src="/imgs/program.jpg" />
        </q-card-section>
      </q-card>
    </div>
    <div class="col-12 col-md-5 q-pa-sm">
      <q-card
        flat
        class="my-card bg-white"
      >
        <q-card-section>
          <q-tabs
            v-model="tab"
            inline-label
            no-caps
            dense
            align="justify"
            indicator-color="warning"
          >
            <q-tab name="login" icon="ti-user" label="Login" />
            <q-tab name="register" icon="ti-write" label="Register" />
          </q-tabs>

          <q-tab-panels v-model="tab">
            <q-tab-panel name="login">
              <div class="fit row wrap justify-center items-center content-center q-pa-xl">
                <div class="col-12 text-grey-8">Embrace the new year with a fresh opportunity!</div>
                
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
                    label="Login"
                    @click="submitLogin"
                  />
                </div>
              </div>
            </q-tab-panel>
          </q-tab-panels>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import { Device } from '@capacitor/device'
import login from '../firebase/firebase-login'

export default {
  name:"IndexPage",
  props: {
    for: String
  },
  data() {
    return {
      tab: "login",
      keepLogin: false,
      loginLoad: false,
      deviceID: "",
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
      services: [
        {
          title: "Available Scholarship",
          description: "List of scholarship available and check for the requirements.",
          icon: "ti-layout-list-thumb",
          buttonAction: "View List",
          action: () => { return false },
        },
        {
          title: "Apply Scholarship",
          description: "Check your eligibility for the scholarship program you want to apply",
          icon: "mdi-book-edit-outline",
          buttonAction: "View List",
          action: () => { return false },
        },
        {
          title: "Verification",
          description: "Verification of the application and current status of your scholarship program",
          icon: "mdi-check-decagram",
          buttonAction: "View List",
          action: () => { return false },
        },
      ]
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
.my-card{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
</style>