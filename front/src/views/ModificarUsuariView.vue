<template>
    <v-sheet   class="mx-auto mt-14 centersheet" >
        <v-row justify="center" no-gutters>
            <v-col class="text-left" cols="12">
                <h1>Modifica l'usuari</h1>
            </v-col>
        </v-row>
        <v-row  >
            <v-col cols="12" >
        <v-form validate-on="input" @submit.prevent="submit"  v-model="valid" class="mt-4">
            <h2>Email</h2>
            <v-text-field
            v-model="email"
            :rules="emailRules"
            label= ""
            required
            clearable
            class="mt-2"
          ></v-text-field>
          <h2>Nom i cognoms</h2>
          <v-text-field
            v-model="nom"
            clearable
            :rules="nomRules"
            label="Escriu el teu nom"
            class="mt-2"
          ></v-text-field>
         
          
          <v-row>
            <v-col md="5">
                <v-switch
             v-model="rol"
             hide-details
             inset
             true-value="g"
             false-value="t"
            :label="`Rol: ${rol}`"
            color="primary"
             ></v-switch>
            </v-col>
            <v-col md="5">
                <v-switch
             v-model="actiu"
             hide-details
             inset
             true-value="true"
             false-value="false"
             
            :label="`Actiu: ${actiu}`"
            color="primary"
             ></v-switch>
            </v-col>
          </v-row>
         

            <v-btn 
            class="mt-4"
            block
          color="primary"
          type="submit"
          size="x-large"
          @click="modificaUsuari()"
          >
          Modifica
          </v-btn>
       
          
        </v-form>
    </v-col>
    </v-row>
        

    </v-sheet>



</template>
<script>
import axios from 'axios'

export default{
    
    data: () => ({
        dadesRaw: {},
        actiu: false,
        id_usuari :"",
      rol: 'g', 
      nom: "",
      valid: true,
      email: "",
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

      nomRules: [
        value => {
            if(value) return true
            return 'El nom és necessàri.'
        },
        
           value => (value || '').length <= 50 || 'Max 50 characters',
        
      ]
     
    }),
    methods: {

      modificaUsuari(){
      axios.put("http://localhost/api/usuari/modificar",{},{
        headers: {'token' : sessionStorage.token ,
        'informacioUsuari':'{"id_usuari":'+this.id_usuari+',"nom":"'+this.nom+'","email":"'+this.email+'","rol":"'+this.rol+'","actiu":'+this.actiu+'}'
                  
                }
      })
      .then(resposta=>{
        console.log(resposta);
        if(resposta.data){
          this.$router.push('/llista-usuari')
        }
        else{window.alert("Usuari no ha pogut ser modificat, torna-ho a intentar")}
      })
    }
  },
  beforeMount(){

        this.dadesRaw = JSON.parse(sessionStorage.getItem("usuariAModificar"));
        console.log(this.dadesRaw);
        this.nom = this.dadesRaw.nom;
        this.email = this.dadesRaw.email;
        this.rol = this.dadesRaw.rol;
        this.id_usuari = this.dadesRaw.id_usuari;
        

  }
    
    

  }






</script>