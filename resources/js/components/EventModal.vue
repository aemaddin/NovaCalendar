<template>
  <modal @modal-close="handleClose" classWhitelist="flatpickr-calendar">
    <form
        @submit.prevent="handleConfirm"
        slot-scope="props"
        class="bg-white rounded-lg shadow-lg overflow-hidden"
        style="width: 460px">
      <div class="p-8">
        <heading v-if="!currentEvent" :level="2" class="mb-6">{{ __('Create Event') }}</heading>
        <heading v-if="currentEvent" :level="2" class="mb-6">{{ __('Edit Event') }}</heading>
        <div class="border-b border-40 pb-4">
          <label class="block mb-2 text-80 leading-tight">Title:</label>
          <input v-model="title" name="title" class="w-full form-control form-input form-input-bordered"/>
        </div>
        <div class="border-b border-40 py-4">
          <label class="block mb-2 text-80">Model:</label>
          <select v-model="eventable_type" @change="getEventableItems"
                  class="w-full form-control form-input form-input-bordered" id="eventable_type">
            <option value="">Please select one model</option>
            <option v-for="value in eventables" :value="value">{{ value }}</option>
          </select>
        </div>
        <div v-if="eventable_type" class="border-b border-40 py-4">
          <label class="block mb-2 text-80">Model Item:</label>
          <select v-model="eventable_id" class="w-full form-control form-input form-input-bordered" id="eventable_type">
            <option value="">Please select one from {{ eventable_type }}</option>
            <option v-for="value in eventable_items" :value="Object.values(value)[0]">{{
                Object.values(value)[1]
              }}
            </option>
          </select>
        </div>
        <div class="border-b border-40 py-4">
          <label class="block mb-2 text-80 leading-tight">Start:</label>
          <date-time-picker @change="changeStart" v-model="start" name="start"
                            class="w-full form-control form-input form-input-bordered" autocomplete="off"/>
        </div>
        <div class="border-b border-40 py-4">
          <label class="block mb-2 text-80">End:</label>
          <date-time-picker @change="changeEnd" v-model="end" name="end"
                            class="w-full form-control form-input form-input-bordered" autocomplete="off"/>
        </div>
      </div>

      <div class="flex justify-between bg-30 px-6 py-3">
        <button v-if="currentEvent" @click.prevent="handleDelete" type="button"
                class="btn btn-default btn-danger flex justify-center items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-labelledby="delete"
               class="fill-current">
            <path fill-rule="nonzero"
                  d="M6 4V2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2h5a1 1 0 0 1 0 2h-1v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6H1a1 1 0 1 1 0-2h5zM4 6v12h12V6H4zm8-2V2H8v2h4zM8 8a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1zm4 0a1 1 0 0 1 1 1v6a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1z"></path>
          </svg>
        </button>

        <div>
          <button @click.prevent="handleClose" type="button" class="btn text-80 font-normal h-9 px-3 btn-link">
            {{ __('Cancel') }}
          </button>

          <button @click.prevent="handleSave" ref="saveButton" type="submit" class="btn btn-default btn-primary ml-3">
            {{ __('Save') }}
          </button>
        </div>
      </div>
    </form>
  </modal>
</template>

<script>
export default {
  props: ['currentEvent', 'currentDate'],
  data() {
    return {
      eventables: [],
      title: this.currentEvent !== null ? this.currentEvent.event.title : '',
      eventable_items: [],
      eventable_type: null,
      display_field: 'title',
      eventable_id: null,
      start: moment(this.currentEvent !== null ? this.currentEvent.event.start : this.currentDate.date).format('YYYY-MM-DD HH:mm:ss'),
      end: this.currentEvent !== null ? moment(this.currentEvent.event.end).format('YYYY-MM-DD HH:mm:ss') : moment(this.currentDate.date).add(1, 'hour').format('YYYY-MM-DD HH:mm:ss')
    }
  },
  methods: {
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
      Nova.request()
          .delete('/nova-vendor/nova-calendar/events/' + this.currentEvent.event.id + '/destroy')
          .then(response => {
            if (response.data.success) {
              this.$toasted.show('Event has been deleted', {type: 'success'});
              this.$emit('close');
              this.$emit('refreshEvents');
            }
          });
    },
    handleSave() {
      let data = {
        title: this.title,
        eventable_id: this.eventable_id,
        eventable_type: this.eventable_type,
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
        Nova.request()
            .put('/nova-vendor/nova-calendar/events/' + this.currentEvent.event.id + '/update', data)
            .then(response => {
              if (response.data.success) {
                this.$toasted.show('Event has been updated', {type: 'success'});
                this.$emit('close');
                this.$emit('refreshEvents');
              } else if (response.data.error === true) {
                this.$toasted.show(response.data.message, {type: 'error'});
              }
            });
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