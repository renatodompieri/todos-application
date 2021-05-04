<template>
  <div class="sidebar-left">
    <div class="sidebar">
      <div class="sidebar-content todo-sidebar">
        <div class="todo-app-menu">
          <div class="add-todo">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              block
              @click="$emit('update:is-todo-handler-sidebar-active', true); $emit('close-left-sidebar')"
            >
              Add Todo
            </b-button>
          </div>
          <vue-perfect-scrollbar
            :settings="perfectScrollbarSettings"
            class="sidebar-menu-list scroll-area"
          >
            <!-- Filters -->
            <b-list-group class="list-group-filters">
              <b-list-group-item
                v-for="filter in todoFilters"
                :key="filter.title + $route.path"
                :to="filter.route"
                :active="isDynamicRouteActive(filter.route)"
                @click="$emit('close-left-sidebar')"
              >
                <feather-icon
                  :icon="filter.icon"
                  size="18"
                  class="mr-75"
                />
                <span class="align-text-bottom line-height-1">{{ filter.title }}</span>
              </b-list-group-item>
            </b-list-group>
          </vue-perfect-scrollbar>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { BButton, BListGroup, BListGroupItem } from 'bootstrap-vue'
import { isDynamicRouteActive } from '@core/utils/utils'
import Ripple from 'vue-ripple-directive'

export default {
  directives: {
    Ripple,
  },
  components: {
    BButton,
    BListGroup,
    BListGroupItem,
    VuePerfectScrollbar,
  },
  setup() {
    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
    }

    const todoFilters = [
      { title: 'My Todos', icon: 'MailIcon', route: { name: 'todo' } },
      { title: 'Important', icon: 'StarIcon', route: { name: 'todo-filter', params: { filter: 'important' } } },
      { title: 'Completed', icon: 'CheckIcon', route: { name: 'todo-filter', params: { filter: 'completed' } } },
      { title: 'Deleted', icon: 'TrashIcon', route: { name: 'todo-filter', params: { filter: 'deleted' } } },
    ]

    return {
      perfectScrollbarSettings,
      todoFilters,
      isDynamicRouteActive,
    }
  },
}
</script>

<style>

</style>
