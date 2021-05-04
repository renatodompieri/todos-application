export default [
  // *===============================================---*
  // *--------- TODO & IT'S FILTERS -------------*
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
]
