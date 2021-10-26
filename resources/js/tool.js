import Tool from "@/components/Tool";

Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-calendar',
      path: '/nova-calendar',
      component: Tool,
    },
  ])
})
