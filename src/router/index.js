import { createRouter, createWebHistory } from 'vue-router'

// Layouts
import ClientLayout from '../layouts/ClientLayout.vue'

// Client Views
import ClientMapView from '../views/client/ClientMapView.vue'
import ClientJobsView from '../views/client/ClientJobsView.vue'
import { portalRoleService } from '../services/portalRoleService'

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
        meta: { title: 'Bản đồ số GDNN tỉnh Ninh Bình' }
      },
      {
        path: 'co-so-gdnn',
        alias: 'danh-sach-co-so',
        name: 'ClientSchoolList',
        component: () => import('../views/client/ClientSchoolListView.vue'),
        meta: { title: 'Danh sách cơ sở GDNN — Tỉnh Ninh Bình' }
      },
      {
        path: 'danh-sach-doanh-nghiep',
        alias: ['doanh-nghiep-gdnn', 'doanh-nghiep-doi-tac'],
        name: 'ClientEnterpriseList',
        component: () => import('../views/client/ClientEnterpriseListView.vue'),
        meta: { title: 'Danh sách doanh nghiệp liên kết — GDNN Ninh Bình' }
      },
      {
        path: 'danh-sach-doanh-nghiep/:id',
        name: 'ClientEnterpriseDetail',
        component: () => import('../views/client/ClientEnterpriseDetailView.vue'),
        meta: { title: 'Thông tin doanh nghiệp — GDNN Ninh Bình' }
      },
      {
        path: 'tuyen-dung',
        alias: 'viec-lam',
        name: 'ClientJobs',
        component: ClientJobsView,
        meta: { title: 'Nghề nghiệp và việc làm — GDNN Ninh Bình' }
      },
      {
        path: 'tuyen-dung/:id',
        alias: 'viec-lam/:id',
        name: 'ClientJobDetail',
        component: () => import('../views/client/ClientJobDetailView.vue'),
        meta: { title: 'Chi tiết tin tuyển dụng — GDNN Ninh Bình' }
      },
      {
        path: 'vai-tro/nha-truong',
        name: 'ClientSchoolPortal',
        component: () => import('../views/client/ClientSchoolPortalView.vue'),
        meta: { title: 'Không gian Cơ sở GDNN — Ninh Bình', portalRole: 'school' }
      },
      {
        path: 'vai-tro/doanh-nghiep',
        name: 'ClientBusinessPortal',
        component: () => import('../views/client/ClientBusinessPortalView.vue'),
        meta: { title: 'Không gian Doanh nghiệp — GDNN Ninh Bình', portalRole: 'business' }
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
  const panelPath = ['/admin', '/truong', '/doanh-nghiep'].find(path => to.path === path || to.path.startsWith(path + '/'))
  if (panelPath) {
    window.location.href = window.location.origin.includes('localhost:517')
      ? `http://localhost:8000${to.fullPath}`
      : to.fullPath
    return false
  }

  const requiredPortalRole = to.meta.portalRole
  if (requiredPortalRole && ! portalRoleService.canAccess(requiredPortalRole)) {
    next('/map')
    return
  }

  if (to.meta.title) {
    document.title = to.meta.title
  }
  next()
})

export default router
