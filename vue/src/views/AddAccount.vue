<template>
  <div class="d-flex align-items-center py-4 mt-5 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
      <form>
        <img class="mb-4" src="https://www.staging13.uptrendcredit.com/wp-content/uploads/2022/04/Uptrend-Credit_landscape-e1650562398898.png" alt="" width="72" height="57">
        <h1 class="h3 mb-3 fw-normal">Connect Instition</h1>
        
        <button class="btn btn-primary w-100 py-2" type="submit" @click="connectAccount">Connect Account</button>
      </form>
    </main>
  </div>
</template>

<style scoped>

</style>

<script setup>
  import { ref } from 'vue';

  const linkToken = ref(null);

  function connectAccount(e) {
    e.preventDefault();

    if (typeof Plaid === 'undefined') {
      console.error('Plaid library is not loaded');
      return;
    }

    if (typeof Plaid.create === 'undefined') {
      console.error('Plaid.create is not available');
      return;
    }

    if (linkToken) {
      generateLinkToken().then(() => {
        initializeLink();
      });
    } else {
      initializeLink();
    }


  }

  function generateLinkToken() {
    return fetch('https://api.uptrendcredit.local/api/plaid/create-link-token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    })
    .then(response => response.json())
    .then(data => {
      linkToken.value = data.link_token;
      console.log(linkToken.value)
    })
    .catch(error => {
      console.error('Error generating link token:', error);
    });
  }

  function initializeLink() {
    const linkHandler = Plaid.create({
      token: linkToken.value,
      onSuccess: (publicToken, metadata) => {
        // Handle successful Plaid Link integration
        console.log('Public Token:', publicToken);
        console.log('Account ID:', metadata.account_id);
        // Send the publicToken to your server for further processing
        sendPublicToken(publicToken);
      },
      onExit: () => {
        // Handle user exiting the Plaid Link integration
        console.log('Plaid Link closed');
      },
    });

    linkHandler.open();
  }

  function sendPublicToken(publicToken) {
    fetch('https://api.uptrendcredit.local/api/plaid/exchange-public-token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ public_token: publicToken }),
    })
    .then(response => response.json())
    .then(data => {
      console.log(data);
    })
    .catch(error => {
      console.error('Error sending public token:', error);
    });
  }
</script>