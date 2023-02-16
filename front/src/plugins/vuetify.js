// Styles
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

// Vuetify
import { createVuetify } from 'vuetify'

export default createVuetify({
  // https://vuetifyjs.com/en/introduction/why-vuetify/#feature-guides
  theme: {
    themes: {
      light: {
        dark: false,
        colors: {
          primary: "#232323",
          secondary: "#F8F5F4",
          background: "#F8F5F4",
          surface: "#F8F5F4",
          success:  "#0AFFBA",
          warning:  "#FFFF7D",
          error:  "#FF7A90",
          info:  "#F8F5F4"
        }
      },
    },
  },
})
