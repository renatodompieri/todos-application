<template>
  <div>
    <b-sidebar
      id="sidebar-todo-handler"
      sidebar-class="sidebar-lg"
      :visible="isTodoHandlerSidebarActive"
      bg-variant="white"
      shadow
      backdrop
      no-header
      right
      @change="(val) => $emit('update:is-todo-handler-sidebar-active', val)"
      @hidden="clearForm"
    >
      <template #default="{ hide }">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center content-sidebar-header px-2 py-1">
          <b-button
            v-if="todoLocal.id"
            size="sm"
            :variant="todoLocal.isCompleted ? 'outline-success' : 'outline-secondary'"
            @click="todoLocal.isCompleted = !todoLocal.isCompleted"
          >
            {{ todoLocal.completed_at !== null ? 'Completed' : 'Mark Complete' }}
          </b-button>
          <h5
            v-else
            class="mb-0"
          >
            Add Todo
          </h5>
          <div>
            <feather-icon
              v-show="todoLocal.id"
              icon="TrashIcon"
              class="cursor-pointer"
              @click="$emit('remove-todo'); hide();"
            />
            <feather-icon
              class="ml-1 cursor-pointer"
              icon="StarIcon"
              size="16"
              :class="{ 'text-warning': todoLocal.important }"
              @click="todoLocal.important = !todoLocal.important"
            />
            <feather-icon
              class="ml-1 cursor-pointer"
              icon="XIcon"
              size="16"
              @click="hide"
            />
          </div>
        </div>

        <!-- Body -->
        <validation-observer
          #default="{ handleSubmit }"
          ref="refFormObserver"
        >

          <!-- Form -->
          <b-form
            class="p-2"
            @submit.prevent="handleSubmit(onSubmit)"
            @reset.prevent="resetForm"
          >

            <!-- Title -->
            <validation-provider
              #default="validationContext"
              name="Title"
              rules="required"
            >
              <b-form-group
                :label="$t('todo.title')"
                label-for="todo-title"
              >
                <b-form-input
                  id="todo-title"
                  v-model="todoLocal.title"
                  autofocus
                  :state="getValidationState(validationContext)"
                  trim
                  placeholder="Todo Title"
                />

                <b-form-invalid-feedback>
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!-- Assignee -->
            <b-form-group
              :label="$t('todo.assignee')"
              label-for="assignee"
            >
              <v-select
                v-model="todoLocal.assignee"
                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                :options="assigneeOptions"
                label="name"
                class="assignee-selector"
                input-id="assignee"
              >

                <template #option="{ avatar, name }">
                  <b-avatar
                    size="26"
                    :src="avatar"
                  />
                  <span class="ml-50 d-inline-block align-middle"> {{ name }}</span>
                </template>

                <template #selected-option="{ avatar, name }">
                  <b-avatar
                    size="26"
                    :src="avatar"
                    :variant="`light-${resolveAvatarVariant(todoLocal.tags)}`"
                    :text="avatarText(name)"
                  />

                  <span class="ml-50 d-inline-block align-middle"> {{ name }}</span>
                </template>
              </v-select>
            </b-form-group>

            <!-- due Date -->
            <validation-provider
              #default="validationContext"
              :name="$t('todo.due_date')"
            >

              <b-form-group
                :label="$t('todo.due_date')"
                label-for="due-date"
              >
                <flat-pickr
                  v-model="todoLocal.date"
                  class="form-control"
                />
                <b-form-invalid-feedback :state="getValidationState(validationContext)">
                  {{ validationContext.errors[0] }}
                </b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>

            <!--Tag -->
            <b-form-group
              :label="$t('todo.tag')"
              label-for="tag"
            >
              <v-select
                v-model="todoLocal.tags"
                :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                multiple
                :close-on-select="false"
                :options="tagOptions"
                :reduce="option => option.value"
                input-id="tags"
              />
            </b-form-group>

            <!-- Description -->
            <b-form-group
              :label="$t('todo.description')"
              label-for="todo-description"
            >
              <quill-editor
                id="quil-content"
                v-model="todoLocal.description"
                :options="editorOption"
                class="border-bottom-0"
              />
              <div
                id="quill-toolbar"
                class="d-flex justify-content-end border-top-0"
              >
                <!-- Add a bold button -->
                <button class="ql-bold" />
                <button class="ql-italic" />
                <button class="ql-underline" />
                <button class="ql-align" />
                <button class="ql-link" />
              </div>
            </b-form-group>

            <!-- Form Actions -->
            <div class="d-flex mt-2">
              <b-button
                v-ripple.400="'rgba(255, 255, 255, 0.15)'"
                variant="primary"
                class="mr-2"
                type="submit"
              >
                {{ todoLocal.id ? 'Update' : 'Add ' }}
              </b-button>
              <b-button
                v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                type="reset"
                variant="outline-secondary"
              >
                Reset
              </b-button>
            </div>
          </b-form>
        </validation-observer>
      </template>
    </b-sidebar>
  </div>
</template>

<script>
import {
  BSidebar, BForm, BFormGroup, BFormInput, BAvatar, BButton, BFormInvalidFeedback,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import Ripple from 'vue-ripple-directive'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, email, url } from '@validations'
import { avatarText } from '@core/utils/filter'
import formValidation from '@core/comp-functions/forms/form-validation'
import { toRefs } from '@vue/composition-api'
import { quillEditor } from 'vue-quill-editor'
import useTodoHandler from './useTodoHandler'

export default {
  components: {
    // BSV
    BButton,
    BSidebar,
    BForm,
    BFormGroup,
    BFormInput,
    BAvatar,
    BFormInvalidFeedback,

    // 3rd party packages
    vSelect,
    flatPickr,
    quillEditor,

    // Form Validation
    ValidationProvider,
    ValidationObserver,
  },
  directives: {
    Ripple,
  },
  model: {
    prop: 'isTodoHandlerSidebarActive',
    event: 'update:is-todo-handler-sidebar-active',
  },
  props: {
    isTodoHandlerSidebarActive: {
      type: Boolean,
      required: true,
    },
    assigneeOptions: {
      type: Array,
      required: true,
    },
    todo: {
      type: Object,
      required: true,
    },
    clearTodoData: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      required,
      email,
      url,
    }
  },
  setup(props, { emit }) {
    const {
      todoLocal,
      resetTodoLocal,

      onSubmit,
      tagOptions,
      resolveAvatarVariant,
    } = useTodoHandler(toRefs(props), emit)

    const {
      refFormObserver,
      getValidationState,
      resetForm,
      clearForm,
    } = formValidation(resetTodoLocal, props.clearTodoData)

    const editorOption = {
      modules: {
        toolbar: '#quill-toolbar',
      },
      placeholder: '',
    }

    return {
      // Add New
      todoLocal,
      onSubmit,
      tagOptions,

      // Form Validation
      resetForm,
      clearForm,
      refFormObserver,
      getValidationState,

      // UI
      editorOption,
      resolveAvatarVariant,

      // Filter/Formatter
      avatarText,
    }
  },
}
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-select.scss';
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/quill.scss';
</style>

<style lang="scss" scoped>
@import '~@core/scss/base/bootstrap-extended/include';

.assignee-selector {
  ::v-deep .vs__dropdown-toggle {
  padding-left: 0;
  }
}

#quil-content ::v-deep {
  > .ql-container {
    border-bottom: 0;
  }

  + #quill-toolbar {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-left-radius: $border-radius;
    border-bottom-right-radius: $border-radius;
  }
}
</style>
