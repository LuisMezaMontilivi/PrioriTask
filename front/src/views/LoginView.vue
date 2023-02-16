<template>
    <v-sheet   class="mx-auto mt-14 centersheet " >
        <v-row justify="center" no-gutters>
            <v-col class="text-center" cols="12">
                <h1>Inicia Sessió</h1>
            </v-col>
        </v-row>
        <v-row  >
            <v-col cols="12" >
        <v-form validate-on="input" @submit.prevent="submit"  v-model="valid" class="mt-4">
            <h2>Email</h2>
            <v-text-field
            v-model="email"
            :rules="emailRules"
            label="Escriu el teu e-mail"
            required
            clearable
            class="mt-2"
          ></v-text-field>
          <h2>Password</h2>
          <v-text-field
            v-model="password"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.min]"
            :type="show1 ? 'text' : 'password'"
            name="input-10-1"
            label="Escriu la teva contrasenya"
            hint="At least 8 characters"
            counter
            required
            clearable
            class="mt-2"
            @click:append="show1 = !show1"
          ></v-text-field>

            <v-btn 
            class="mt-4"
            block
          color="primary"
          size="x-large"
          @click="iniciarSessio()"
          

          >
          Inicia
          </v-btn>
       
          
        </v-form>
        <h3>{{ token }}</h3>
        <h3>{{ usr }}</h3>
    </v-col>
    </v-row>
        

    </v-sheet>



</template>


<script>
import axios from 'axios'
export default{

    data: () => ({
      valid: false,
      email: '',
      emailRules: [
        value => {
          if (value) return true

          return 'E-mail is requred.'
        },
        value => {
          if (/.+@.+\..+/.test(value)) return true

          return 'E-mail must be valid.'
        },
      ],
      show1: false,
        show2: true,
        password: '',
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= 8 || v=="1234" || 'Min 8 characters' ,
        },
        token: "",
        usr: "",
        rol:"",
    }),
    methods: {
        tokenInicial(){
      axios.put("http://localhost/api/token/obtenir",{},{
        headers: {'apikey': "GuillemEsUnOient"}
      }
      )
        .then(resposta => {
          this.token = resposta.data;
        })
    },
    iniciarSessio(){
      axios.put("http://localhost/api/usuari/iniciar",{},{
        headers: {'token' : this.token,
                  'maininfo':'{"usuari": "'+ this.email + '", "contrasenyaEncriptada": "'+ this.password +'"}' }
                  
      })
      .then(resposta=>{
        this.usr = resposta.data;
        sessionStorage.setItem("usuari", this.email);
        sessionStorage.setItem("token", resposta.data.token);
        sessionStorage.setItem("rol", resposta.data.rol);
        sessionStorage.setItem("logejat", true);
        this.rol = resposta.data.rol;
        console.log(resposta.data.ultima_peticio);
        if(resposta.data.ultima_peticio == null){
            this.$router.push('/canvi-contrasenya');
        }
        else if( resposta.data.rol == 'a' ){
            this.$router.push('/principal-admin');
        }
        else if(resposta.data.rol == 'g' || resposta.data.rol == 't'){
            this.$router.push('/modificar-tasca');
        }
      })
    }    
    },
    beforeMount(){
        this.tokenInicial()
       

    }
  }






</script>