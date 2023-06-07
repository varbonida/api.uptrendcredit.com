<template>
  <div class="main-container">
    <header class="top-bar py-2">
      <nav class="container-fluid navbar navbar-expand-lg">
        <div class="container-fluid">
          <div class="navbar-header me-4">
            <a class="navbar-brand" href="/">Uptrend Credit
              <!-- <img src="/src/assets/Uptrend-Credit_landscape_white.png" class="img-responsive" alt="Uptrend Credit"> -->
            </a>
          </div>
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="/">
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="/accounts">
                Accounts
              </a>
            </li>
          </ul>
          <div class="right-side">
            <ul class="navbar-nav">
              <!-- <li class="nav-item search-box d-none d-md-flex align-items-center">
                <div class="nav-link">
                  <form class="app-search">
                    <input type="text" class="form-control rounded-pill border-0" placeholder="Search for...">
                    <a class="srh-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search feather-sm fill-white mt-n1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></a>
                  </form>
                </div>
              </li> -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img src="/src/assets/vue.svg" alt="user" width="30" class="profile-pic rounded-circle">
                </a>
              </li>
            </ul>
          </div>
          <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <!-- <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Offcanvas</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
              <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                  </ul>
                </li>
              </ul>
              <form class="d-flex mt-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
              </form>
            </div>
          </div> -->
        </div>
      </nav>
    </header>
    <div class="page-container container d-block py-4">
      <router-view></router-view>
    </div>
  </div>
</template>

<style scoped>

</style>

<script setup>
</script>