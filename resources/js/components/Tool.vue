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
import listPlugin from '@fullcalendar/list';
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
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        initialView: 'dayGridMonth',
        locale: Nova.config.fullcalendar_locale || 'en',
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        eventDrag: this.handleDrag,
        eventDrop: this.handleDrop,
        eventChange: this.handleChange,
        eventsSet: this.handleChange,
        eventResize: this.handleDrop,
        dayMaxEvents: true,
        eventResizableFromStart: true,
        editable: true,
        selectable: true,
        selectMirror: true,
        weekends: true,
        navLinks: true,
        nowIndicator: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: false
        },
        timeFormat: 'H(:mm)',
      },
      currentEvent: null,
      currentDate: null,
      showModal: false
    }
  },
  methods: {
    handleDrag(date) {

    },
    handleDrop(requestDate) {
      Nova.request()
          .put('/nova-vendor/nova-calendar/events/' + requestDate.event.id + '/update', {
            title: requestDate.event.title,
            eventable_type: requestDate.event._def.extendedProps.eventable_name,
            eventable_id: requestDate.event._def.extendedProps.eventable_id,
            start: moment.utc(requestDate.event.start).format('YYYY-MM-DD HH:mm:ss'),
            end: moment.utc(requestDate.event.end).format('YYYY-MM-DD HH:mm:ss'),
          })
          .then(response => {
            if (response.data.success) {
              this.$toasted.show('Event has been updated', {type: 'success'});
            } else if (response.data.error === true) {
              this.$toasted.show(response.data.message, {type: 'error'});
            }
            this.$emit('refreshEvents');
          })
          .catch(response => {
            requestDate.revert();
            this.$toasted.show(response.data.message, {type: 'error'})
          });
    },
    handleChange(info) {
      //
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