
<template>
  <q-layout view="lHh LpR lFf">

    <q-header reveal class="bg-primary">
      <q-toolbar>
        
        <!-- <q-btn 
          dense 
          flat 
          round 
          icon="menu"
          @click="toggleLeftDrawer" 
        /> -->
        
        <q-btn
          color="primary"
          round
          unelevated
          :icon="miniState ? 'ti-menu' : 'ti-menu-alt'"
          class="drawerBtn"
          @click="toggleLeftDrawer"
        />
        <q-item>
          <!-- <q-item-section side>
            <q-avatar round size="32px">
              <img src="https://cdn.quasar.dev/img/avatar.png">
            </q-avatar>
          </q-item-section> -->
          <!-- <q-item-section>
            <q-item-label>{{`${userDetails.fullName}`}}</q-item-label>
          </q-item-section> -->
        </q-item>
        
        
        <q-space />
        <q-btn class="q-mr-sm" round dense flat icon="ti-bell" @click="drawerRight = !drawerRight">
          <q-badge floating color="red" rounded transparent>
            {{ notifCount }}
          </q-badge>
        </q-btn>
        <!-- <q-btn class="q-mr-sm" round dense flat icon="ti-help" /> -->
        
        
      </q-toolbar>
    </q-header>

    <q-drawer 
      show-if-above 
      v-model="leftDrawerOpen"
      :mini="miniState"
      side="left" 
      bordered
    >
      <div class="row q-pa-md q-mt-lg">
        <div class="col-12 text-bold text-h5 text-orange">
          <q-img v-if="miniState" src="/imgs/ASCT_logo.png" />
          <q-img v-if="!miniState" src="/imgs/ASCT_logo2.png" />
          <!-- <q-icon name="ti-pie-chart" /> <span v-if="!miniState">Accounting IS</span> -->
        </div>
      </div>
      <!-- drawer content -->
      <!-- <Profile v-bind="userProfile" /> -->
      <q-separator dark />
      <SideNav 
        v-for="link in filteredMenus"
        :key="link.title"
        v-bind="link"
      />

      <div class="fixed-bottom q-pa-sm q-mb-md">
        <!-- <q-btn v-if="miniState" color="primary" icon="ti-layout-grid2" size="md" round>
          <q-menu
          style="min-width: 200px"
            anchor="bottom right"
            self="bottom left"
            :offset="[20, 20]"
          >
            <q-list style="min-width: 200px">
              <q-item  @click="logout">
                <q-item-section clickable v-ripple>
                  <q-item-section avatar>
                    <q-icon color="primary" name="ti-share-alt" />
                  </q-item-section>
                  <q-item-section>Logout</q-item-section>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn> -->

        
        <q-card v-if="!miniState" class="myMenuBar">
          <q-card-section class="fit row wrap justify-center items-center content-start myMenuBarSection">
            <q-btn-group flat dense spread>
              <q-btn @click="goToProfile" flat size="sm" rounded color="primary" icon="ti-user"/>
              <q-btn @click="modalStatus = true" flat rounded size="sm" color="secondary" icon="ti-lock"/>
              <q-btn flat rounded color="red" icon="ti-power-off" size="sm" @click="logout" />
            </q-btn-group>
          </q-card-section>
        </q-card>
      </div>
    </q-drawer>

    <q-page-container>
      <q-page class="q-pa-sm">
        <div style="height: 88vh;">
          <router-view />
        </div>
      </q-page>
    </q-page-container>

    <!-- <q-footer reveal class="bg-text-weight-thin text-blue-white-9 text-center q-pt-lg q-pb-lg">
      <div>{{ `Centralize Distribution and Sales Inventory System ©2023 Created by FWDS` }}</div>
    </q-footer> -->
    <q-dialog persistent v-model="modalStatus">
      <q-card style="width: 500px; max-width: 80vw;">
          <q-card-section>
              <div class="text-h6">Change Password</div>
          </q-card-section>

          <q-separator />

          <q-card-section style="max-height: 60vh" class="scroll">
             <q-form ref="passForm" class="q-gutter-md">
                  <q-input 
                      class="q-pt-md" 
                      bottom-slots
                      outlined
                      v-model="newPassword"
                      v-bind="formRules.matchPass"
                      :type="isPwd ? 'password' : 'text'"
                      label="New Password" 
                      :dense="true"
                  >
                      <template v-slot:prepend>
                          <q-icon name="password" />
                      </template>
                      <template v-slot:append>
                          <q-icon
                              :name="isPwd ? 'visibility_off' : 'visibility'"
                              class="cursor-pointer"
                              @click="isPwd = !isPwd"
                          />
                      </template>
                  </q-input>

                  <q-input 
                      class="q-pt-md" 
                      bottom-slots 
                      v-model="retypePass"
                      outlined
                      v-bind="formRules.matchPass"
                      :type="isPwd ? 'password' : 'text'"
                      label="Re-type Password" 
                      :dense="true"
                  >
                      <template v-slot:prepend>
                          <q-icon name="password" />
                      </template>
                      <!-- <template v-slot:append>
                          <q-icon
                              :name="isPwd ? 'visibility_off' : 'visibility'"
                              class="cursor-pointer"
                              @click="isPwd = !isPwd"
                          />
                      </template> -->
                  </q-input>
             </q-form>
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
              <q-btn flat label="Cancel" :loading="btnLoading" color="negative" @click="cancelChange" />
              <q-btn flat label="Submit" :loading="btnLoading" color="positive" @click="submitChangePass" />
          </q-card-actions>
      </q-card>
    </q-dialog>


    <q-drawer
      side="right"
      v-model="drawerRight"
      bordered
      overlay
      :width="400"
    >
      <q-scroll-area class="fit">
        <q-card
          flat
          class=" bg-white"
        >
            <q-card-section class="row items-center no-wrap">
                <div>
                  <div class="text-h5 text-weight-bold">Notifications</div>
                </div>
            </q-card-section>
            <q-separator />
            <q-card-section >
              <q-list>
                <q-item 
                  v-for="(notif, index) in notifList"
                  :key="index"
                  clickable
                  @click="getScholarshipDetails(notif.applicationId)"
                >
                <q-item-section avatar>
                  <q-icon v-if="notif.notifType === 'red'" name="mdi-bell-alert" :color="notif.notifType" />
                  <q-icon v-if="notif.notifType === 'green'" name="mdi-check-decagram" :color="notif.notifType" />
                </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ `${notif.sender.firstName} ${notif.sender.lastName} `}}</q-item-label>
                    <q-item-label caption lines="2">{{ notif.message }}</q-item-label>
                    <q-item-label caption>{{ moment(notif.createdDate).format("LL LT") }}</q-item-label>
                  </q-item-section>
                  <!-- <q-separator inset /> -->
                </q-item>

                
              </q-list>
            </q-card-section>
        </q-card>
      </q-scroll-area>
    </q-drawer>
  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import SideNav from '../components/Templates/Sidenav.vue';
import Profile from '../components/Templates/Profile.vue';
// import Crumbs from '../components/Templates/Breadcrumbs.vue';
import moment from 'moment';
import MenuJson from './menus.json'
import { jwtDecode } from 'jwt-decode';

const dateNow = moment().format('YYYY-MM-DD');

export default {
  name:"UserLoggedPage",
  data(){
    return {
      // linksList,
      userProfile: null,
      notifList:[],
      notifCount:0,
      drawerRight: false,
      menuCrumbs: [
        {label: '', icon: 'home', link: '/'},
        {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
      ],
      leftDrawerOpen: true,
      miniState: false,
      cutOffDate: "",
      notifStartDay: false,
      // Change Password
      modalStatus: false,
      newPassword:'',
      retypePass: '',
      isPwd: true,
      formRules: {
          matchPass: {
              rules: [
                  val => !!val || this.$t('validations_error.empty'),
                  val => val == this.newPassword || this.$t('validations_error.invalid_match'),
              ]
          },
      },
    }
  },
  watch:{
    drawerRight(newVal){
      this.seenNotif()
      this.getNotification()
    },
    "$router.currentRoute.value": {
      handler: function(){
        this.checkModule();
      },
      deep: true
    }
  },
  components:{
    SideNav,
    Profile,
  },
  computed: {
    filteredMenus: function(){
      return MenuJson;
    },
    userDetails(){
      const user = LocalStorage.getItem('userData')
      return jwtDecode(user);
    }
  },
  created(){
    this.getNotificationCount()
    this.checkModule();
  },
  methods: {
    moment,
    checkModule() {

      let filterMenuRoutes = MenuJson.filter((el) => {
        if(el.children){
          let childFilter = el.children.filter((child) => {
            return child.link === this.$router.currentRoute.value.name && this.userDetails.modules.includes(child.code)
          })
          return childFilter
        } else {
          return el.link === this.$router.currentRoute.value.name && this.userDetails.modules.includes(el.code)
        }
        
      })

      if(filterMenuRoutes.length === 0){
        this.$router.push('/401')
      }

      // return this.userDetails.modules.includes(id) ? true : false;
    },
    goToProfile(){
      this.$router.push('/user/profile')
    },
    async getNotification(){
      this.notifList = []
      this.$api.post('misc/get/notifications', {uId: this.userDetails.userId}).then((response) => {
        const data = {...response.data};
        if(!data.error){
          let dataList = data.list.sort((a, b) => +(a.createdDate < b.createdDate) || -(a.createdDate > b.createdDate))
          this.notifList = dataList
        }
      })
    },
    async getNotificationCount(){
      this.$api.post('misc/get/notifications/unseen', {uId: this.userDetails.userId}).then((response) => {
        const data = {...response.data};
        if(!data.error){
          this.notifCount = data.list.length
        }
      })
    },
    async getScholarshipDetails($appId){
      this.$api.post('scholarship/get/details', {appId: $appId}).then((response) => {
        console.log(response.data)
      })
    },
    async seenNotif(){
      this.$api.post('misc/update/notification', {uId: this.userDetails.userId, type: 'seen'}).then((response) => {
        const data = {...response.data};
        this.notifCount = 0
      })
    },
    changePassModal(){
      this.modalStatus = true;
    },
    cancelChange(){
      this.newPassword = '',
      this.retypePass = '',
      this.modalStatus = false;
    },
    toggleLeftDrawer () {
      this.miniState = !this.miniState
    },
    setCrumbsItem(val){
      this.menuCrumbs = val;
    },
    logout(){
      this.$q.dialog({
          title: 'Logout',
          message: 'Are you sure you want to logout?',
          ok: {
              label: 'Yes'
          },
          cancel: {
              label: 'No',
              color: 'negative'
          },
          persistent: true
      }).onOk(() => {
        localStorage.removeItem('userData');
        this.$router.push('/')
      })
      
    },
    async submitChangePass(){
      let vm = this;
      let payload = {
          id: this.userDetails.userId,
          newPassword: this.retypePass
      };
      let compoDetails = this.$refs.passForm;

      compoDetails.validate().then(success => {

          if(!success){
              this.$q.notify({
                  color: 'negative',
                  position: 'top-right',
                  title: 'Incomplete Form',
                  message: 'Please fill the required fields',
                  icon: 'report_problem'
              })
              return false;
          } else {
              // Confirm
              this.$q.dialog({
                  title: 'Change Password?',
                  message: 'Are you sure you want to change your password?',
                  ok: {
                      label: 'Yes'
                  },
                  cancel: {
                      label: 'No',
                      color: 'negative'
                  },
                  persistent: true
              }).onOk(() => {
                  this.$q.loading.show({
                      message: 'Changing your password. Please wait...'
                  });

                  this.$api.post('users/changePassword', payload).then((response) => {
        
                      const data = {...response.data};
                      if(!data.error){
                          LocalStorage.remove('userData');
                          vm.$router.push('/')
                      } else {
                          vm.$q.notify({
                              color: 'negative',
                              position: 'top-right',
                              title:data.title,
                              message: vm.$t(`errors.${data.error}`),
                              icon: 'report_problem'
                          })
                      }
                  })

                  this.$q.loading.hide();

              })
          }
      })
    }
  }
}
</script>

<style scoped>
.myMenuBar{
  padding: 0% !important;
  border-radius: 20px;
}
.myMenuBarSection{
  padding: 3% !important;
}
.drawerBtn{
  position: absolute;
  left: -20px;
}
.bg-header-custom{
  background: #003978;
}
</style>