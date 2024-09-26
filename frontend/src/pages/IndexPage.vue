<template>
  <div class="row">
    <div class="col-12 col-md-7 q-pa-sm">
      <q-card
        flat
        class="my-card bg-white"
      >
        <q-card-section>
          <span class="text-h6 text-bold">ASCOT Scholarship Services</span><br/>
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
                    {{item.buttonAction}}
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
              <LoginForm />
            </q-tab-panel>
            <q-tab-panel name="register">
              <RegisterForm />
            </q-tab-panel>
          </q-tab-panels>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script>
import LoginForm from '../components/Forms/Login.vue'
import RegisterForm from '../components/Forms/Registration.vue'

export default {
  name:"IndexPage",
  components:{
    LoginForm,
    RegisterForm
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
  methods: {}
}
</script>

<style scoped>
.my-card{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
</style>