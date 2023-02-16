import { createRouter, createWebHistory } from 'vue-router'
import PrincipalView from '../views/PrincipalView.vue'
import CrearTascaView from '../views/CrearTascaView.vue'
import NotFoundView from '../views/NotFoundView.vue'
import LoginView from '@/views/LoginView.vue'
import CrearUsuariView from '@/views/CrearUsuariView.vue'
import CanviContrasenyaView from '@/views/CanviContrasenyaView.vue'
import LlistaUsuarisView from '@/views/LlistaUsuarisView.vue'
import ModificarUsuariView from '@/views/ModificarUsuariView.vue'

const routes = [
  {
    path: '/',
    name: 'principal',
    component: PrincipalView
  },
  {
  path: '/login',
  name: 'login',
  component: LoginView
  },
  {
    path: '/crear-usuari',
    name: 'crear-usuari',
    component: CrearUsuariView
  },
  {
    path: '/canvi-contrasenya',
    name: 'canvi-contrasenya',
    component: CanviContrasenyaView
  },
  {
    path: '/llista-usuari',
    name: 'llista-usuari',
    component: LlistaUsuarisView
  },
  {
    path: '/modificar-usuari/:id_usuari(\\d+)',
    name: 'modificar-usuari',
    component: ModificarUsuariView,
    
  },

  {
    path: '/crear-tasca',
    name: 'crear-tasca',
    component: CrearTascaView
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundView
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
