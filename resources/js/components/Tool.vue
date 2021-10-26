<template>
  <div>
    <div class="card py-6 px-6">
      <FullCalendar ref="fullCalendar" :options="calendarOptions"/>
    </div>

    <transition name="fade">
      <event-modal
          v-if="showModal"
          :currentEvent="currentEvent"
          :currentDate="currentDate"
          @refreshEvents="refreshEvents"
          @close="closeModal"
          @confirm="saveEvent"
          @delete="deleteEvent"
      />
    </transition>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import EventModal from './EventModal.vue';

export default {
  metaInfo() {
    return {
      title: 'Calendar',
    }
  },
  components: {
    FullCalendar,
    EventModal
  },
  data() {
    return {
      calendarOptions: {
        events: '/nova-vendor/nova-calendar/events',
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: Nova.config.fullcalendar_locale || 'en',
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        eventDrag: this.handleDrag,
        eventDrop: this.handleDrop,
        dayMaxEvents: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: false
        },
        timeFormat: 'H(:mm)',
        editable: true
      },
      currentEvent: null,
      currentDate: null,
      showModal: false
    }
  },
  methods: {
    handleDrag(date) {

      // console.log(date);
    },
    handleDrop(requestDate) {
      console.log(requestDate)
      Nova.request()
          .put('/nova-vendor/nova-calendar/events/' + requestDate.event.id + '/update', {
            title: requestDate.event.title,
            start: moment.utc(requestDate.event.start).format('YYYY-MM-DD HH:mm:ss'),
            end: moment.utc(requestDate.event.end).format('YYYY-MM-DD HH:mm:ss'),
          })
          .then(response => {
            if (response.data.success) {
              this.$toasted.show('Event has been updated', {type: 'success'});
              this.$emit('refreshEvents');
            } else if (response.data.error === true) {
              this.$toasted.show(response.data.message, {type: 'error'});
            }
          })
          .catch(response => this.$toasted.show('Something went wrong', {type: 'error'}));
    },
    handleDateClick(date) {
      this.showModal = true;
      this.currentDate = date;
    },
    handleEventClick(event) {
      this.showModal = true;
      this.currentEvent = event;
    },
    closeModal() {
      this.showModal = false;
      this.currentEvent = null;
      this.currentDate = null;
    },
    saveEvent() {

    },
    deleteEvent() {

    },
    refreshEvents() {
      this.$refs.fullCalendar.getApi().refetchEvents();
    }
  },
}
</script>