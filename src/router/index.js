import { createRouter, createWebHistory } from 'vue-router'

// Layouts
import ClientLayout from '../layouts/ClientLayout.vue'

// Client Views
import ClientMapView from '../views/client/ClientMapView.vue'

const routes = [
  // Client Portal Routes
  {
    path: '/',
    component: ClientLayout,
    children: [
      {
        path: '',
        redirect: '/map'
      },
      {
        path: 'map',
        name: 'ClientMap',
        component: ClientMapView,
        meta: { title: 'Bản đồ số Giáo dục Tỉnh Ninh Bình' }
      },
      {
        path: 'thong-ke',
        redirect: '/map'
      },
      {
        path: 'gioi-thieu',
        redirect: '/map'
      }
    ]
  },

  // Catch-all redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/map'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guard: If user types /admin or any /admin/* URL, instantly navigate to Filament v5
router.beforeEach((to, from, next) => {
  if (to.path.startsWith('/admin')) {
    window.location.href = window.location.origin.includes('localhost:517')
      ? 'http://localhost:8000/admin'
      : '/admin'
    return false
  }

  if (to.meta.title) {
    document.title = `${to.meta.title} — Bản đồ số Ninh Bình`
  }
  next()
})

export default router
