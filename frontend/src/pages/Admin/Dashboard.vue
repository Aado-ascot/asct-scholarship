<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Application</span><br/>
            </div>
        </div>
        <div class="row">
            <div 
                v-for="(item, index) in dashboardCards" 
                :key="index"
                class="col-12 col-xs-12 col-sm-6 col-md-3 q-pa-sm"
            >
                <q-card class="my-card" flat bordered>
                    <q-card-section class="text-h6">
                        {{ item.title }}
                    </q-card-section>
                    <q-item>
                        <q-item-section avatar>
                            <q-avatar :color="item.iconColor"  rounded>
                                <q-icon :name="item.icon" color="white" size="34px" />
                            </q-avatar>
                        </q-item-section>

                        <q-item-section>
                            <q-item-label class="text-h4">{{ item.count }}</q-item-label>
                            <q-item-label caption>
                                {{ item.description }}
                            </q-item-label>
                        </q-item-section>
                    </q-item>
                    
                </q-card>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    data(){
        return {
            dashboardCards: [
                {
                    title: "Users",
                    count: 0,
                    description: "Students registered in the portal",
                    icon: "mdi-account-school-outline",
                    iconColor: "primary",
                },
                {
                    title: "Qualified Students",
                    count: 0,
                    description: "Students get approved on the programs",
                    icon: "mdi-thumb-up",
                    iconColor: "green",
                },
                {
                    title: "Qualified Students",
                    count: 0,
                    description: "Students are declined on the programs",
                    icon: "mdi-thumb-down",
                    iconColor: "red",
                },
                {
                    title: "Pendings",
                    count: 0,
                    description: "Applications subject for evaluation and approval ",
                    icon: "mdi-clipboard-text-clock",
                    iconColor: "secondary",
                },
            ]
        }
    },
    computed: {
        user: function(){
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
    },
    mounted(){
        this.getDasboard()
    },
    methods: {
        moment,
        async getDasboard(){
            this.$api.get('misc/dashboard').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dashboardCards[0].count = data.users
                    this.dashboardCards[1].count = data.qualified
                    this.dashboardCards[2].count = data.unqualified
                    this.dashboardCards[3].count = data.pendings
                    console.log(data)
                } else {
                   console.log('error something went wrong')
                }
            })
        },
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 10px;
}
.generatedDash{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(255 251 176) 0%, rgb(0 55 255 / 61%) 89%);
}
.generatedDash2{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(38 148 243) 0%, rgb(45 253 215 / 61%) 89%);
}
</style>
