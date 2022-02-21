<template>
  <div>
    <div class="card py-6 px-6">
      <FullCalendar ref="fullCalendar" :options="calendarOptions"/>
    </div>

    <transition name="fade">
      <div>
        <event-modal
            v-if="showModal"
            :currentEvent="currentEvent"
            :currentDate="currentDate"
            @refreshEvents="refreshEvents"
            @close="closeModal"
            @delete="openDeleteModal"
            @update="openUpdateModal"
        />

        <confirm-action-modal
            ref="deleteActionModal"
            v-if="showHandleDelete"
            :action="handleDeleteAction"
            resource-name="events"
            @confirm="handleDelete"
            @close="closeDeleteModal">
        </confirm-action-modal>

        <confirm-action-modal
            ref="updateActionModal"
            v-if="showHandleUpdate"
            :action="handleUpdateAction"
            resource-name="events"
            @confirm="handleUpdate"
            @close="closeUpdateModal">

        </confirm-action-modal>
      </div>
    </transition>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import EventModal from '@/views/EventModal.vue';
import ConfirmActionModal from "@/components/Modals/ConfirmActionModal";

export default {
  metaInfo() {
    return {
      title: 'Calendar',
    }
  },
  components: {
    ConfirmActionModal,
    FullCalendar,
    EventModal
  },
  data() {
    return {
      initialLoading: true,
      loading: false,
      showHandleDelete: false,
      showHandleUpdate: false,
      currentEvent: null,
      dataToUpdate: null,
      currentDate: null,
      showModal: false,
      handleDeleteAction: {
        name: 'Delete Event',
        confirmText: 'Do you want to apply these changes to all future recurring events, too?',
        cancelButtonText: "Cancel",
        confirmButtonText: "Delete",
        class: 'btn-danger',
        fields: [
          // {
          //   attribute: "recurrences",
          //   component: "boolean-field",
          //   helpText: 'Do you want to delete all recurrences also?',
          //   indexName: "recurrences",
          //   name: "recurrences",
          //   nullable: false,
          //   panel: null,
          //   prefixComponent: true,
          //   readonly: false,
          //   required: false,
          //   sortable: false,
          //   sortableUriKey: "relations",
          //   stacked: false,
          //   textAlign: "center",
          //   validationKey: "relations"
          // }
        ]
      },
      handleUpdateAction: {
        name: 'Update Event',
        confirmText: 'Do you want to apply these changes to all future recurring events, too?',
        cancelButtonText: "Cancel",
        confirmButtonText: "Update",
        class: 'btn-primary',
        fields: []
      },
      calendarOptions: {
        timeZone: Nova.config.novaCalendar.timezone || 'UTC',
        events: '/nova-vendor/nova-calendar/events',
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        initialView: 'dayGridMonth',
        locale: Nova.config.novaCalendar.locale || 'en',
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        eventDrop: this.handleDrop,
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
        timeFormat: 'HH:mm:ss',
      }
    }
  },
  methods: {
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
    openDeleteModal(event) {
      this.showHandleDelete = true;
      this.currentEvent = event;
    },
    closeDeleteModal() {
      this.showHandleDelete = false;
      this.currentEvent = null;
      this.currentDate = null;
    },
    handleDelete() {
      // console.log(this.$refs.deleteActionModal.$refs.recurrences[0].value)
      Nova.request()
          .delete('/nova-vendor/nova-calendar/events/' + this.currentEvent.event.id + '/destroy')
          .then(response => {
            if (response.data.success) {
              this.$toasted.show('Event has been deleted', {type: 'success'});
              this.closeDeleteModal();
              this.refreshEvents();
            }
          });
    },
    openUpdateModal(event, data) {
      this.showHandleUpdate = true;
      this.currentEvent = event;
      this.dataToUpdate = data;
    },
    closeUpdateModal() {
      this.showHandleUpdate = false;
      this.currentEvent = null;
      this.currentDate = null;
    },
    handleUpdate() {
      let data = this.dataToUpdate;
      Nova.request()
          .put('/nova-vendor/nova-calendar/events/' + this.currentEvent.event.id + '/update', data)
          .then(response => {
            if (response.data.success) {
              this.$toasted.show('Event has been updated', {type: 'success'});
              this.closeUpdateModal();
              this.refreshEvents();
            } else if (response.data.error === true) {
              this.$toasted.show(response.data.message, {type: 'error'});
            }
          });
    },
    refreshEvents() {
      this.$refs.fullCalendar.getApi().refetchEvents();
    }
  },
}
</script>