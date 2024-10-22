<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Application</span><br/>
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
    computed: {
        user: function(){
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
    },
    mounted(){
        // this.getSchedules().then((res) => {
        //     this.checkLoanStatuses()
        //     this.$nextTick(() => {
        //         this.getDashboard()
        //         this.getDailyDashboard()
        //     })
        // })
    },
    methods: {
        moment,
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
