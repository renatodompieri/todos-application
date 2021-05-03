import axios from '@axios'

export default {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    fetchTasks(ctx, payload) {
      return new Promise((resolve, reject) => {
        axios
          .get('v1/todo', { params: payload })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    addTask(ctx, taskData) {
      return new Promise((resolve, reject) => {
        axios
          .post('v1/todo', { taskData })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    updateTask(ctx, { task }) {
      return new Promise((resolve, reject) => {
        axios
          .patch(`v1/todo/${task.id}`, { task })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    removeTask(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`v1/todo/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
