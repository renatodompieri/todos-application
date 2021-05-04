import Vue from 'vue'
import VueRouter from 'vue-router'

// Routes
//   @todo: Disabled until passport is configured
// import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn } from '@/auth/utils'
import todo from './routes/todo'
import authentication from './routes/authentication'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    { path: '/', redirect: { name: 'todo' } },
    ...todo,
    ...authentication,
    {
      path: '*',
      redirect: 'error-404',
    },
  ],
})

router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()
  if (!isLoggedIn && to.name !== 'auth-login') return next({ name: 'auth-login' })
  /**
   if (!canNavigate(to)) {
    // Redirect to login if not logged in

    // If logged in => not authorized
    return next({ name: 'misc-not-authorized' })
  }
  */

  /**
    if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    const userData = getUserData()
    next(getHomeRouteForLoggedInUser(userData ? userData.role : null))
  }
 */

  return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
