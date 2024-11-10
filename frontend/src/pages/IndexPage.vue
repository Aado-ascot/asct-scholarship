<template>
  <div class="row">
    <div class="col-12 col-md-7 q-pa-sm">
      <q-card
        flat
        class="my-card bg-white"
      >
        <q-card-section>
          <span class="text-h6 text-bold">ASCOT Announcements</span><br/>
          <q-list class="rounded-borders">

            <q-item 
              v-for="(itm, indx) in announcements"
              :key="indx"
            >
              <q-item-section avatar>
                <q-avatar>
                  <q-icon name="newspaper" size="md" />
                </q-avatar>
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1">{{itm.title}}</q-item-label>
                <q-item-label caption lines="2">
                  <span class="text-weight-bold">Context: </span>
                  {{itm.description}}
                </q-item-label>
              </q-item-section>

              <q-item-section side top>
                1 min ago
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <q-img :src="`/imgs/ASCOT-WEBSITE.png`" />
          <span class="text-h6 text-bold">Mandate</span><br/>
          <span class="text-title">
            The State College shall primarily provide technical and professional training in the sciences, arts, teacher education, agriculture, engineering and technology as well as short-term vocational courses.  It shall likewise promote research, advanced studies and academic leadership in the stated areas of specialization. (Section 2 of RA7664)
            <br /><br />
            <a 
              href="https://ascot.edu.ph/wp-content/uploads/2009/07/RA7664.pdf"
              target="_blank"
              class="text-bold text-primary"
              style="text-decoration: none;"
            >RA7664</a> The law creating the Aurora State College of Technology
          </span>
          <q-list class="rounded-borders q-mt-md">

            <q-item>
              <q-item-section avatar>
                <q-avatar>
                  <q-icon name="mdi-eye" size="md" />
                </q-avatar>
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1"><span class="text-bold">Vision</span></q-item-label>
                <q-item-label caption>
                  <span class="text-weight-bold">ASCOT 2030: </span>
                  ASCOT as a globally recognized comprehensive inclusive higher education institution anchoring on the local culture of Aurora in particular and the Philippines in general.
                </q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section avatar>
                <q-avatar>
                  <q-icon name="mdi-bullseye-arrow" size="md" />
                </q-avatar>
              </q-item-section>

              <q-item-section>
                <q-item-label lines="1"><span class="text-bold">Mission</span></q-item-label>
                <q-item-label caption>
                  ASCOT shall capacitate human resources of Aurora and beyond to be globally – empowered and future proofed; generate, disseminate and apply knowledge and technologies for sustainable development.
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
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
import moment from 'moment'
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
      announcements: [
        {
          title: "Sample Test Announcement",
          description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
          datePosted: moment().format('MMM DD, YYYY')
        },
        {
          title: "Sample Test Announcement2",
          description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
          datePosted: moment().format('MMM DD, YYYY')
        },
        {
          title: "Sample Test Announcement3",
          description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
          datePosted: moment().format('MMM DD, YYYY')
        },
      ],
    }
  },
  methods: {
    moment
  }
}
</script>

<style scoped>
.my-card{
    border-radius: 10px;
    box-shadow: 0px 0px 3px -2px !important;
}
</style>