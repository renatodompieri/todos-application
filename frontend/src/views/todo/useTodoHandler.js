import { ref, watch } from '@vue/composition-api'
// import store from '@/store'

export default function useTodoHandler(props, emit) {
  // ------------------------------------------------
  // todoLocal
  // ------------------------------------------------
  const todoLocal = ref(JSON.parse(JSON.stringify(props.todo.value)))
  const resetTodoLocal = () => {
    todoLocal.value = JSON.parse(JSON.stringify(props.todo.value))
  }
  watch(props.todo, () => {
    resetTodoLocal()
  })

  const onSubmit = () => {
    const todoData = JSON.parse(JSON.stringify(todoLocal))
    const todoObj = todoData.value
    const filteredTodo = {
      id: todoObj.id || null,
      assignee_id: todoObj.assignee.id || null,
      title: todoObj.title || null,
      description: todoObj.description || null,
      status: 1,
      date: todoObj.date || null,
      tags: todoObj.tags || null,
      important: todoObj.important || null,
    }

    // * If event has id => Edit Event
    // Emit event for add/update event
    if (props.todo.value.id) emit('update-todo', filteredTodo)
    else emit('add-todo', filteredTodo)

    // Close sidebar
    emit('update:is-todo-handler-sidebar-active', false)
  }

  /* eslint-enable global-require */

  const resolveAvatarVariant = tags => {
    if (tags.includes('high')) return 'primary'
    if (tags.includes('medium')) return 'warning'
    if (tags.includes('low')) return 'success'
    if (tags.includes('update')) return 'danger'
    if (tags.includes('team')) return 'info'
    return 'primary'
  }

  const tagOptions = [
    { label: 'Team', value: 'team' },
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
    { label: 'Update', value: 'update' },
  ]

  return {
    todoLocal,
    resetTodoLocal,
    // UI
    resolveAvatarVariant,
    tagOptions,
    onSubmit,
  }
}
