<template>
    <div class="row">
        <div class="col-12 text-grey-8 q-mb-md">Embrace the new year with a fresh opportunity! Join with us to create you future.</div>

        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
            <q-input
                v-model="form.username"
                label="Username"
                v-bind="formRules.username"
                placeholder="Enter Username / Student Number"
                outlined 
                stack-label
            >
                <template v-slot:prepend>
                <q-icon name="ti-user" />
                </template>
            </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
            <q-input 
                v-model="form.password"
                v-bind="formRules.password"
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

        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.lastName"
            label="Last Name"
            placeholder="Enter Last Name"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.firstName"
            label="First Name"
            placeholder="Enter First Name"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.middleName"
            label="Middle Name"
            placeholder="Enter Middle Name"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.suffix"
            label="Suffix"
            placeholder="Ex. JR, II, III, IV, V"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-select 
            outlined 
            v-model="form.sex" 
            :options="sexOpt" 
            label="Gender" 
            stack-label 
            options-dense
          >
          </q-select>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-select 
            outlined 
            v-model="form.civilStatus" 
            :options="statusOpt" 
            label="Civil Status" 
            stack-label 
            options-dense
          >
          </q-select>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.email"
            label="Email"
            placeholder="Enter Email Address"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.contact"
            label="Conatct Number"
            placeholder="Enter Mobile Number"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            type="date"
            v-model="form.dateOfBirth"
            label="Birth Date"
            placeholder="MM/DD/YYYY"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.placeOfBirth"
            label="Birth Place"
            placeholder="Enter Place of Birth"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.address"
            type="textarea"
            label="Address"
            placeholder="Enter Permanent Address"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.schoolAttended"
            label="Name of School"
            placeholder="Enter Last School Attended"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.schoolAddress"
            type="textarea"
            label="School Address"
            placeholder="Enter School Address"
            outlined
            stack-label
          >
          </q-input>
        </div>


        <div class="col-12 q-mt-lg">
            <q-btn
                type="submit"
                class="full-width q-pa-md btn-custom-border" 
                unelevated
                :loading="loginLoad"
                no-caps 
                color="primary" 
                label="Register"
                @click="submitRegister"
            />
        </div>
    </div>
</template>

<script>
import { LocalStorage } from 'quasar'

export default {
  name:"RegisterComponent",
  data() {
    return {
      tab: "login",
      keepLogin: false,
      loginLoad: false,
      sexOpt: [
        "Male",
        "Female",
        "Prefer Not To Say"
      ],
      statusOpt: [
        "Single",
        "Married",
        "Widowed",
        "Separated",
      ],
      form: {
        username: "",
        password: "",
        firstName: "",
        lastName: "",
        middleName: "",
        suffix: "",
        sex: "Male",
        civilStatus: "Single",
        email: "",
        contact: "",
        dateOfBirth: "",
        placeOfBirth: "",
        address: "",
        schoolAttended: "",
        schoolAddress: "",
        userType: 2
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
    async submitRegister(){
      this.$q.loading.show();
      this.loginLoad = true;
      let vm = this;
      let payload = vm.form;

      this.$api.post('users/register', payload).then(async (response) => {
        const data = {...response.data};
        if(!data.error){
          this.submitLogin();
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
    },
    async submitLogin(){
      let vm = this;
      let payload = {
        username: vm.form.username,
        password: vm.form.password
      };

      this.$api.post('auth/login', payload).then(async (response) => {
        const data = {...response.data};
        if(!data.error){
          this.clearForm();
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
    },

    clearForm(){
      this.form = {
        username: "",
        password: "",
        firstName: "",
        lastName: "",
        middleName: "",
        suffix: "",
        sex: "Male",
        civilStatus: "Single",
        email: "",
        contact: "",
        dateOfBirth: "",
        placeOfBirth: "",
        address: "",
        schoolAttended: "",
        schoolAddress: "",
        userType: 2
      }
    }
  }
}
</script>
