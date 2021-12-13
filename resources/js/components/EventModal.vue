<template>
  <modal
      data-testid="event-modal"
      tabindex="-1"
      role="dialog"
      classWhitelist="flatpickr-calendar"
      :closes-via-backdrop="canLeave"
      @modal-close="handleClose">

    <form
        autocomplete="off"
        @keydown="handleKeydown"
        @submit.prevent.stop="handleConfirm"
        class="bg-white overflow-hidden rounded-lg shadow-lg"
        slot-scope="props">

      <div style="width: 500px">
        <heading :level="2" class="border-b border-40 py-8 px-8 flex justify-between items-center">
          {{ !currentEvent ? __('Create Event') : __('Edit Event') }}
          <div class="flex">
            <a :href="'/nova/resources/events/' + currentEvent.event.id"
               class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center has-tooltip"
               data-original-title="null">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 16" aria-labelledby="view"
                   class="fill-current">
                <path
                    d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path>
              </svg>
            </a>
            <a :href="'/nova/resources/events/' + currentEvent.event.id + '/attach/users?viaRelationship=users&polymorphic=0'"
               title="Add Participants"
               class="btn btn-default btn-primary flex items-center justify-center px-3" v-if="currentEvent">
              <add-user-icon class="fill-current text-white"/>
            </a>
          </div>
        </heading>

        <div>
          <!-- Validation Errors -->
          <validation-errors :errors="errors"/>

          <form-section>
            <template v-slot:label>
              <form-label label="Title"/>
            </template>
            <template v-slot:content>
              <form-text id="title" v-model="title"/>
            </template>
          </form-section>


          <form-section>
            <template v-slot:label>
              <form-label label="Eventable Type"/>
            </template>

            <template v-slot:content>
              <form-select id="eventable_type"
                           dusk="eventable_type"
                           label="Eventable Type"
                           :options="eventables"
                           v-model="eventable_type"
                           @change="getEventableItems"/>
            </template>
          </form-section>

          <form-section v-if="eventable_type">
            <template v-slot:label>
              <form-label label="Model Item"/>
            </template>

            <template v-slot:content>
              <form-object-select id="eventable_id"
                                  dusk="eventable_id"
                                  label="Eventable"
                                  :options="eventable_items"
                                  value-name="id"
                                  title-name="title"
                                  v-model="eventable_id"/>
            </template>
          </form-section>

          <form-section>
            <template v-slot:label>
              <form-label label="Start"/>
            </template>
            <template v-slot:content>
              <date-time-picker @change="changeStart" v-model="start" name="start"
                                class="w-full form-control form-input form-input-bordered" autocomplete="off"/>
            </template>
          </form-section>

          <form-section>
            <template v-slot:label>
              <form-label label="End"/>
            </template>
            <template v-slot:content>
              <date-time-picker @change="changeEnd" v-model="end" name="end"
                                class="w-full form-control form-input form-input-bordered" autocomplete="off"/>
            </template>
          </form-section>

          <form-section>
            <template v-slot:label>
              <label class="inline-block text-80 leading-tight">Recurrence</label>
            </template>
            <template v-slot:content>
              <div v-for="(item, index) in recurrences">
                <label :for="`recurrences_${item.value}`" class="inline-flex items-center">
                  <input :id="`recurrences_${item.value}`" type="radio" class="form-radio"
                         name="recurrences" v-model="recurrence"
                         :value="item.value" :checked="index === 0"/>
                  <span class="ml-2">{{ item.name }}</span>
                </label>
              </div>
            </template>
          </form-section>
        </div>
      </div>

      <div class="bg-30 px-6 py-3 flex">
        <button v-if="currentEvent" @click.prevent="handleDelete" type="button"
                class="btn btn-default btn-danger flex justify-center items-center">
          <trash-icon/>
        </button>

        <div class="flex items-center ml-auto">
          <button @click.prevent="handleClose" type="button"
                  class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6">
            {{ __('Cancel') }}
          </button>

          <button @click.prevent="handleSave" ref="saveButton" type="submit" class="btn btn-default btn-primary">
            {{ confirmButtonText }}
          </button>
        </div>
      </div>
    </form>
  </modal>
</template>

<script>
import FormText from "@/components/Fields/FormText";
import FormSelect from "@/components/Fields/FormSelect";
import FormLabel from "@/components/Fields/FormLabel";
import FormSection from "@/components/Fields/FormSection";
import FormObjectSelect from "@/components/Fields/FormObjectSelect";
import FormRadioGroup from "@/components/Fields/FormRadioGroup";
import TrashIcon from "@/components/Icons/TrashIcon";
import AddUserIcon from "@/components/Icons/AddUserIcon";

export default {
  components: {AddUserIcon, TrashIcon, FormRadioGroup, FormObjectSelect, FormSection, FormLabel, FormSelect, FormText},
  props: ['currentEvent', 'currentDate'],
  data() {
    return {
      errors: null,
      canLeave: true,
      confirmButtonText: this.currentEvent !== null ? 'Update' : 'Create',
      title: this.currentEvent !== null ? this.currentEvent.event.title : '',
      start: this.currentEvent !== null ? moment(this.currentEvent.event.start).format('YYYY-MM-DD HH:mm:ss') : this.currentDate.allDay ? moment(this.currentDate.date).add(8, 'hour').format('YYYY-MM-DD HH:mm:ss') : moment(this.currentDate.date).format('YYYY-MM-DD HH:mm:ss'),
      end: this.currentEvent !== null ? moment(this.currentEvent.event.end).format('YYYY-MM-DD HH:mm:ss') : this.currentDate.allDay ? moment(this.currentDate.date).add(9, 'hour').format('YYYY-MM-DD HH:mm:ss') : moment(this.currentDate.date).add(0.5, 'hour').format('YYYY-MM-DD HH:mm:ss'),
      eventables: [],
      eventable_id: null,
      eventable_type: null,
      eventable_items: [],
      recurrence: this.currentEvent !== null ? this.currentEvent.event.extendedProps.recurrence : 'none',
      recurrences: [
        {name: 'None', value: 'none'},
        {name: 'Daily', value: 'daily'},
        {name: 'Weekly', value: 'weekly'},
        {name: 'Monthly', value: 'monthly'},
      ]
    }
  },
  methods: {
    /**
     * Stop propogation of input events unless it's for an escape or enter keypress
     */
    handleKeydown(e) {
      if (['Escape', 'Enter'].indexOf(e.key) !== -1) {
        return
      }

      e.stopPropagation()
    },
    getEventables() {
      Nova.request().get('/nova-vendor/nova-calendar/eventables')
          .then(response => {
            this.eventables = response.data
          });
    },
    getEventableItems() {
      if (!this.eventable_id) {
        this.eventable_id = 1;
      }
      Nova.request().get('/nova-vendor/nova-calendar/eventables/' + this.eventable_type)
          .then(response => {
            this.eventable_items = response.data
          });
    },
    changeStart(value) {
      this.start = value;
    },
    changeEnd(value) {
      this.end = value;
    },
    handleClose() {
      this.$emit('close');
    },
    handleConfirm() {
      this.handleSave();
      this.$emit('confirm')
    },
    handleDelete() {
      this.$emit('close');
      this.$emit('delete', this.currentEvent)
    },
    handleSave() {
      let data = {
        title: this.title,
        eventable_id: this.eventable_id,
        eventable_type: this.eventable_type,
        recurrence: this.recurrence,
        start: moment.utc(moment(this.start)), // convert time to utc before save it
        end: moment.utc(moment(this.end)), // convert time to utc before save it
      };
      if (this.currentEvent === null) {
        Nova.request()
            .post('/nova-vendor/nova-calendar/events/store', data)
            .then(response => {
              if (response.data.success) {
                this.$toasted.show('Event has been created', {type: 'success'});
                this.$emit('close');
                this.$emit('refreshEvents');
              } else if (response.data.error === true) {
                this.$toasted.show(response.data.message, {type: 'error'});
              }
            });
      } else if (this.currentEvent !== null) {
        this.$emit('close');
        this.$emit('update', this.currentEvent, data);
      }
    },
    getEventableFromCurrentEvent() {
      this.eventable_type = this.currentEvent.event._def.extendedProps.eventable_name;
      this.eventable_id = this.currentEvent.event._def.extendedProps.eventable_id;
      this.getEventableItems();
    }
  },
  mounted() {
    this.getEventables();
    if (this.currentEvent) {
      this.getEventableFromCurrentEvent();
    }
  }
}
</script>