export default [
  // *===============================================---*
  // *--------- TODO & IT'S FILTERS N TAGS -------------*
  // *===============================================---*
  {
    path: '/todo',
    name: 'todo',
    component: () => import('@/views/todo/Todo.vue'),
    meta: {
      contentRenderer: 'sidebar-left',
      contentClass: 'todo-application',
    },
  },
  {
    path: '/todo/:filter',
    name: 'todo-filter',
    component: () => import('@/views/todo/Todo.vue'),
    meta: {
      contentRenderer: 'sidebar-left',
      contentClass: 'todo-application',
      navActiveLink: 'todo',
    },
    beforeEnter(to, _, next) {
      if (['important', 'completed', 'deleted'].includes(to.params.filter)) next()
      else next({ name: 'error-404' })
    },
  },
  {
    path: '/todo/tag/:tag',
    name: 'todo-tag',
    component: () => import('@/views/todo/Todo.vue'),
    meta: {
      contentRenderer: 'sidebar-left',
      contentClass: 'todo-application',
      navActiveLink: 'todo',
    },
    beforeEnter(to, _, next) {
      if (['team', 'low', 'medium', 'high', 'update'].includes(to.params.tag)) next()
      else next({ name: 'error-404' })
    },
  },
]
