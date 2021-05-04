import axios from '@axios'

export default {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    fetchTodos(ctx, payload) {
      return new Promise((resolve, reject) => {
        axios
          .get('v1/todo', { params: payload })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addTodo(ctx, todoData) {
      return new Promise((resolve, reject) => {
        axios
          .post('v1/todo', { todo: todoData })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    updateTodo(ctx, { todo }) {
      return new Promise((resolve, reject) => {
        axios
          .patch(`v1/todo/${todo.id}`, { todo })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    removeTodo(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`v1/todo/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
