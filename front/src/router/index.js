// Composables
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    component: () => import('@/layouts/default/Default.vue'),
    children: [
      {
        path: '',
        name: 'Home',
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import(/* webpackChunkName: "home" */ '@/views/Home.vue'),
      },
    ],
  },
  {
    path: '/inici-administrador',
    name: 'inici-administrador',
    component: iniciAdministradorView
  },
  {
    path: '/primer-login',
    name: 'primer-login',
    component: primerLoginView
  },
  {
    path: '/login',
    name: 'login',
    component: loginView
  },
  {
    path: '/taulell',
    name: 'taulell',
    component: taulellView
  },
  {
    path: '/calendari',
    name: 'calendari',
    component: calendariView
  },
  {
    path: '/crea-tasca',
    name: 'creaTasca',
    component: creaTascaView
  },
  {
    path: '/modifica-tasca/:id-tasca',
    name: 'modifica-tasca',
    component: modificaTascaView
  },
  {
    path: '/usuari/crear',
    name: 'creaUsuari',
    component: creaUsuariView
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
})

export default router