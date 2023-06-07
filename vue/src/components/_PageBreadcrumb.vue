<template>
  <div class="page-breadcrumb">
    <div class="row">
      <div class="col-md-5 align-self-center">
        <h3 class="page-title">{{ pageTitle }}</h3>
        <div class="d-flex align-items-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">
                {{ pageTitle }}
              </li>
            </ol>
          </nav>
        </div>
      </div>
      <!-- <div class="col-md-7 justify-content-end align-self-center d-none d-md-flex">
        <div class="d-flex">
          <div class="dropdown me-2">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              January 2023
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="#">February 2023</a></li>
              <li><a class="dropdown-item" href="#">March 2023</a></li>
              <li><a class="dropdown-item" href="#">April 2023</a></li>
            </ul>
          </div>
          <button class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus fill-white feather-sm"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create
          </button>
        </div>
      </div> -->
    </div>
  </div>
</template>

<style scoped>
</style>

<script setup>
  const props = defineProps({
    pageTitle: String
  })

</script>