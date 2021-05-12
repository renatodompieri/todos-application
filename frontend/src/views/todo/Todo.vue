<template>
  <!-- Need to add height inherit because Vue 2 don't support multiple root ele -->
  <div style="height: inherit">
    <div
      class="body-content-overlay"
      :class="{'show': mqShallShowLeftSidebar}"
      @click="mqShallShowLeftSidebar = false"
    />
    <div class="todo-list">

      <!-- App Searchbar Header -->
      <div class="fixed-search d-flex align-items-center">

        <!-- Toggler -->
        <div class="sidebar-toggle d-block d-lg-none ml-1">
          <feather-icon
            icon="MenuIcon"
            size="21"
            class="cursor-pointer"
            @click="mqShallShowLeftSidebar = true"
          />
        </div>

        <!-- Searchbar -->
        <div class="d-flex align-content-center justify-content-between w-100">
          <b-input-group class="input-group-merge">
            <b-input-group-prepend is-text>
              <feather-icon
                icon="SearchIcon"
                class="text-muted"
              />
            </b-input-group-prepend>
            <b-form-input
              :value="searchQuery"
              placeholder="Search todo"
              @input="updateRouteQuery"
            />
          </b-input-group>
        </div>

        <!-- Dropdown -->
        <div class="dropdown">
          <b-dropdown
            variant="link"
            no-caret
            toggle-class="p-0 mr-1"
            right
          >
            <template #button-content>
              <feather-icon
                icon="MoreVerticalIcon"
                size="16"
                class="align-middle text-body"
              />
            </template>
            <b-dropdown-item @click="resetSortAndNavigate">
              Reset Sort
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'title-asc' } }">
              Sort A-Z
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'title-desc' } }">
              Sort Z-A
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'assignee' } }">
              Sort Assignee
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'due-date' } }">
              Sort Due Date
            </b-dropdown-item>
          </b-dropdown>
        </div>
      </div>

      <!-- Todo List -->
      <vue-perfect-scrollbar
        :settings="perfectScrollbarSettings"
        class="todo-list-wrapper list-group scroll-area"
      >
        <draggable
          v-model="todos"
          handle=".draggable-todo-handle"
          tag="ul"
          class="todo-list media-list"
        >
          <li
            v-for="todo in todos"
            :key="todo.id"
            class="todo-item"
            :class="{ 'completed': todo.completed_at !== null }"
            @click="handleTodoClick(todo)"
          >
            <feather-icon
              icon="MoreVerticalIcon"
              class="draggable-todo-handle d-inline"
            />
            <div class="todo-title-wrapper">
              <div class="todo-title-area">
                <div class="title-wrapper">
                  <b-form-checkbox
                    :checked="todo.completed_at !== null"
                    @click.native.stop
                    @change="updateTodoIsCompleted(todo)"
                  />
                  <span class="todo-title">{{ todo.title }}</span>
                </div>
              </div>
              <div class="todo-item-action">
                <div class="badge-wrapper mr-1">
                  <b-badge
                    v-for="tag in assureJsonElement(todo.tags)"
                    :key="tag"
                    pill
                    :variant="`light-${resolveTagVariant(tag)}`"
                    class="text-capitalize"
                  >
                    {{ tag }}
                  </b-badge>
                </div>
                <small class="text-nowrap text-muted mr-1">{{ formatDate(todo.date, {month: 'short', day: 'numeric'}) }}</small>
                <b-avatar
                  v-if="todo.assignee"
                  size="32"
                  :src="todo.assignee.avatar"
                  :variant="`light-${resolveAvatarVariant(todo.tags)}`"
                  :text="avatarText(todo.assignee.fullName)"
                />
                <b-avatar
                  v-else
                  size="32"
                  variant="light-secondary"
                >
                  <feather-icon
                    icon="UserIcon"
                    size="16"
                  />
                </b-avatar>
              </div>
            </div>

          </li>
        </draggable>
        <div
          class="no-results"
          :class="{'show': !todos.length}"
        >
          <h5>No Items Found</h5>
        </div>
      </vue-perfect-scrollbar>
    </div>

    <!-- Todo Handler -->
    <todo-handler-sidebar
      v-model="isTodoHandlerSidebarActive"
      :assignee-options="assigneeOptions"
      :todo="todo"
      :clear-todo-data="clearTodoData"
      @remove-todo="removeTodo"
      @add-todo="addTodo"
      @update-todo="updateTodo"
    />

    <!-- Sidebar -->
    <portal to="content-renderer-sidebar-left">
      <todo-left-sidebar
        :todo-tags="todoTags"
        :is-todo-handler-sidebar-active.sync="isTodoHandlerSidebarActive"
        :class="{'show': mqShallShowLeftSidebar}"
        @close-left-sidebar="mqShallShowLeftSidebar = false"
      />
    </portal>
  </div>
</template>

<script>
import store from '@/store'
import {
  ref, watch, computed, onUnmounted,
} from '@vue/composition-api'
import {
  BFormInput, BInputGroup, BInputGroupPrepend, BDropdown, BDropdownItem,
  BFormCheckbox, BBadge, BAvatar,
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import draggable from 'vuedraggable'
import { formatDate, avatarText } from '@core/utils/filter'
import { useRouter } from '@core/utils/utils'
import { useResponsiveAppLeftSidebarVisibility } from '@core/comp-functions/ui/app'
import TodoLeftSidebar from './TodoLeftSidebar.vue'
import todoStoreModule from './todoStoreModule'
import TodoHandlerSidebar from './TodoHandlerSidebar.vue'

export default {
  components: {
    BFormInput,
    BInputGroup,
    BInputGroupPrepend,
    BDropdown,
    BDropdownItem,
    BFormCheckbox,
    BBadge,
    BAvatar,
    draggable,
    VuePerfectScrollbar,

    // App SFC
    TodoLeftSidebar,
    TodoHandlerSidebar,
  },
  setup() {
    const TODO_APP_STORE_MODULE_NAME = 'todo'

    // Register module
    if (!store.hasModule(TODO_APP_STORE_MODULE_NAME)) store.registerModule(TODO_APP_STORE_MODULE_NAME, todoStoreModule)

    // UnRegister on leave
    onUnmounted(() => {
      if (store.hasModule(TODO_APP_STORE_MODULE_NAME)) store.unregisterModule(TODO_APP_STORE_MODULE_NAME)
    })

    const { route, router } = useRouter()
    const routeSortBy = computed(() => route.value.query.sort)
    const routeQuery = computed(() => route.value.query.q)
    const routeParams = computed(() => route.value.params)
    watch(routeParams, () => {
      // eslint-disable-next-line no-use-before-define
      fetchTodos()
    })

    const todos = ref([])
    const sortOptions = [
      'latest',
      'title-asc',
      'title-desc',
      'assignee',
      'due-date',
    ]

    const sortBy = ref(routeSortBy.value)
    watch(routeSortBy, val => {
      if (sortOptions.includes(val)) sortBy.value = val
      else sortBy.value = val
    })

    const resetSortAndNavigate = () => {
      const currentRouteQuery = JSON.parse(JSON.stringify(route.value.query))

      delete currentRouteQuery.sort

      router.replace({ name: route.name, query: currentRouteQuery }).catch(() => {
      })
    }
    const assigneeOptions = ref([])
    const prepareSelectElements = () => {
      store.dispatch('todo/prepareSelectElements')
        .then(response => {
          assigneeOptions.value = response.data.users
        })
    }

    prepareSelectElements()

    const blankTodo = {
      id: null,
      title: '',
      date: new Date(),
      description: '',
      assignee: null,
      assignee_id: null,
      tags: [],
      completed_at: null,
      isDeleted: false,
      important: false,
    }

    const todo = ref(JSON.parse(JSON.stringify(blankTodo)))
    const clearTodoData = () => {
      todo.value = JSON.parse(JSON.stringify(blankTodo))
    }

    const addTodo = val => {
      store.dispatch('todo/addTodo', val)
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTodos()
        })
    }
    const removeTodo = () => {
      store.dispatch('todo/removeTodo', { id: todo.value.id })
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTodos()
        })
    }
    const updateTodo = todoData => {
      store.dispatch('todo/updateTodo', todoData)
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTodos()
        })
    }

    const toggleStatus = todoData => {
      store.dispatch('todo/toggleStatus', todoData)
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTodos()
        })
    }

    const perfectScrollbarSettings = {
      maxScrollbarLength: 150,
    }

    const isTodoHandlerSidebarActive = ref(false)

    const todoTags = [
      { title: 'Team', color: 'primary', route: { name: 'todo-tag', params: { tag: 'team' } } },
      { title: 'Low', color: 'success', route: { name: 'todo-tag', params: { tag: 'low' } } },
      { title: 'Medium', color: 'warning', route: { name: 'todo-tag', params: { tag: 'medium' } } },
      { title: 'High', color: 'danger', route: { name: 'todo-tag', params: { tag: 'high' } } },
      { title: 'Update', color: 'info', route: { name: 'todo-tag', params: { tag: 'update' } } },
    ]

    const resolveTagVariant = tag => {
      if (tag === 'team') return 'primary'
      if (tag === 'low') return 'success'
      if (tag === 'medium') return 'warning'
      if (tag === 'high') return 'danger'
      if (tag === 'update') return 'info'
      return 'primary'
    }

    const resolveAvatarVariant = tags => {
      if (tags.includes('high')) return 'primary'
      if (tags.includes('medium')) return 'warning'
      if (tags.includes('low')) return 'success'
      if (tags.includes('update')) return 'danger'
      if (tags.includes('team')) return 'info'
      return 'primary'
    }

    // Search Query
    const searchQuery = ref(routeQuery.value)
    watch(routeQuery, val => {
      searchQuery.value = val
    })
    // eslint-disable-next-line no-use-before-define
    watch([searchQuery, sortBy], () => fetchTodos())
    const updateRouteQuery = val => {
      const currentRouteQuery = JSON.parse(JSON.stringify(route.value.query))

      if (val) currentRouteQuery.q = val
      else delete currentRouteQuery.q

      router.replace({ name: route.name, query: currentRouteQuery })
    }

    const fetchTodos = () => {
      store.dispatch('todo/fetchTodos', {
        q: searchQuery.value,
        filter: router.currentRoute.params.filter,
        tag: router.currentRoute.params.tag,
        sortBy: sortBy.value,
      })
        .then(response => {
          // eslint-disable-next-line
          todos.value = response.data
        })
    }

    fetchTodos()

    // Single Todo isCompleted update
    const updateTodoIsCompleted = todoData => {
      // eslint-disable-next-line no-param-reassign
      toggleStatus(todoData)
    }

    const assureJsonElement = tags => {
      try {
        return JSON.parse(tags)
      } catch (e) {
        return tags
      }
    }

    const handleTodoClick = todoData => {
      todo.value = todoData
      todo.value.tags = assureJsonElement(todo.value.tags)
      isTodoHandlerSidebarActive.value = true
    }

    const { mqShallShowLeftSidebar } = useResponsiveAppLeftSidebarVisibility()

    return {
      todo,
      todos,
      assigneeOptions,
      removeTodo,
      toggleStatus,
      assureJsonElement,
      addTodo,
      updateTodo,
      clearTodoData,
      todoTags,
      searchQuery,
      fetchTodos,
      perfectScrollbarSettings,
      updateRouteQuery,
      resetSortAndNavigate,

      // UI
      resolveTagVariant,
      resolveAvatarVariant,
      isTodoHandlerSidebarActive,

      // Click Handler
      handleTodoClick,

      // Filters
      formatDate,
      avatarText,

      // Single Todo isCompleted update
      updateTodoIsCompleted,

      // Left Sidebar Responsive
      mqShallShowLeftSidebar,
    }
  },
}
</script>

<style lang="scss">
@import "~@core/scss/base/pages/app-todo.scss";
</style>

<style lang="scss" scoped>
.draggable-todo-handle {
  position: absolute;
  left: 8px;
  top: 50%;
  transform: translateY(-50%);
  visibility: hidden;
  cursor: move;

  .todo-list .todo-item:hover & {
    visibility: visible;
  }
}
</style>
