<template>
  <ion-page>
    <ion-content :fullscreen="true">
      <div id="main-layout">
        <div id="payment-information">
          <ion-list lines="none" id="payment-list">
            <ion-item>
              <ion-list lines="none" id="payment-item">
                <ion-item>
                  <ion-grid>
                    <ion-row>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="Adresse"
                          :value="inputFields.Adresse"
                          label-placement="floating"
                          @ionInput="onAddressInput($event)"
                          ref="ionAddressInputEl"
                        ></ion-input>
                      </ion-col>
                    </ion-row>
                    <ion-row>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="Postleitzahl"
                          label-placement="floating"
                          :value="inputFields.Postleitzahl"
                          @ionInput="validatePostal($event)"
                          ref="ionPostalInputEl"
                        >
                        </ion-input>
                      </ion-col>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="Stadt"
                          label-placement="floating"
                          :value="inputFields.Stadt"
                          @ionInput="
                            filterNonAlphaChar($event, 'Stadt', ionCityInputEl)
                          "
                          ref="ionCityInputEl"
                        >
                        </ion-input>
                      </ion-col>
                    </ion-row>
                    <ion-row>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="Vorname"
                          label-placement="floating"
                          :value="inputFields.Vorname"
                          @ionInput="
                            filterNonAlphaChar(
                              $event,
                              'Vorname',
                              ionFirstnameInputEl
                            )
                          "
                          ref="ionFirstnameInputEl"
                        >
                        </ion-input>
                      </ion-col>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="Nachname"
                          label-placement="floating"
                          :value="inputFields.Nachname"
                          @ionInput="
                            filterNonAlphaChar(
                              $event,
                              'Nachname',
                              ionLastnameInputEl
                            )
                          "
                          ref="ionLastnameInputEl"
                        >
                        </ion-input>
                      </ion-col>
                    </ion-row>
                    <ion-row>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="Telefon"
                          label-placement="floating"
                          type="tel"
                          v-model="inputFields.Telefon"
                          placeholder="0000 0000 0000"
                          v-maskito="phoneOptions"
                          maxlength="14"
                        >
                        </ion-input>
                      </ion-col>
                      <ion-col>
                        <ion-input
                          fill="outline"
                          label="E-Mail"
                          label-placement="floating"
                          type="email"
                          v-model="inputFields.Email"
                          error-text="Ungültige E-Mail"
                          @ionInput="validate"
                          @ionBlur="markTouched"
                          ref="emailInputRef"
                        >
                        </ion-input>
                      </ion-col>
                    </ion-row>
                  </ion-grid>
                </ion-item>
              </ion-list>
            </ion-item>
            <ion-checkbox v-model="acceptTOC"
              >Ich akzeptiere die
              <a @click.prevent="openModal">AGBs</a></ion-checkbox
            >
            <ion-modal :is-open="isModalOpen" @didDismiss="closeModal">
              <img src="../../public/img/Edler.png" alt="Luka Karl Elder" />
            </ion-modal>
            <ion-item>
              <ion-button
                @click="confirmOrder"
                id="order-button"
                :disabled="!canOrder()"
                @click.prevent="openConfirmationModal"
                >Bestellen</ion-button
              >
            </ion-item>
          </ion-list>
        </div>
        <div id="shopping-cart">
          <ion-title id="cart-title">Deine Bestellung</ion-title>
          <ion-list lines="none">
            <ion-item
              v-for="(productData, productName) in cart"
              :key="productName"
            >
              <ion-grid>
                <ion-row>
                  <ion-col size="3">
                    <ion-label>{{ productData.amount }}x</ion-label>
                  </ion-col>
                  <ion-col size="3">
                    <ion-label>{{ productName }}</ion-label>
                  </ion-col>
                  <ion-col size="3">
                    <ion-label
                      >{{ prices[productName].toFixed(2) }} €</ion-label
                    >
                  </ion-col>
                </ion-row>
              </ion-grid>
            </ion-item>
          </ion-list>

          <ion-grid>
            <ion-row>
              <ion-col size="3">
                <ion-label>Gesamt:</ion-label>
              </ion-col>
              <ion-col
                size="3"
                style="padding-left: 135px; white-space: nowrap"
              >
                <ion-label>{{ prices["total"].toFixed(2) }} €</ion-label>
              </ion-col>
            </ion-row>
          </ion-grid>
        </div>
      </div>
      <ion-modal
        :is-open="isConfirmationModalOpen"
        @didDismiss="closeConfirmationModal"
      >
        <div id="iconContainer">
          <ion-icon :icon="checkmarkCircleOutline" id="confrimIcon"></ion-icon>
          <ion-label id="confirmationMessage"
            >Vielen Dank für deine Bestellung! Wir sorgen dafür, dass sie so schnell
            wie möglich bei dir ist!</ion-label
          >
        </div>
      </ion-modal>
    </ion-content>
  </ion-page>
</template>

<script setup>
import {
  IonPage,
  IonContent,
  IonList,
  IonItem,
  IonGrid,
  IonRow,
  IonCol,
  IonLabel,
  IonButton,
  IonTitle,
  IonCheckbox,
  IonInput,
  IonModal,
  IonIcon,
} from "@ionic/vue";
import { ref, onMounted } from "vue";
import { maskito as vMaskito } from "@maskito/vue";
import axios from "axios";
import { checkmarkCircleOutline } from "ionicons/icons";
import { useRouter } from "vue-router";

const router = useRouter();

const goto = (path) => {
  router.push(path);
};

const cart = ref({});
const prices = ref({ total: 0 });
const acceptTOC = ref(false);
const emailIsValid = ref(false);

const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const isConfirmationModalOpen = ref(false);
const openConfirmationModal = () => {
  isConfirmationModalOpen.value = true;
};

const closeConfirmationModal = () => {
  isConfirmationModalOpen.value = false;
};
const order = ref({
  total: 0,
  date: "",
  CustomerInfo: {},
  pizzas: {},
});

const inputFields = ref({
  Adresse: "",
  Postleitzahl: "",
  Stadt: "",
  Vorname: "",
  Nachname: "",
  Telefon: "",
  Email: "",
});

const sendDataToDatabase = async (data) => {
  try {
    const response = await axios.post(
      "http://localhost/ionic-pizzawebsite/sendData.php",
      JSON.stringify(data),
      {
        headers: {
          "Content-Type": "application/json",
        },
      }
    );
  } catch (error) {
    console.error("Es gab einen Fehler:", error.message);
  }
};

const canOrder = () => {
  if (
    inputFields.value.Adresse != "" &&
    inputFields.value.Postleitzahl != "" &&
    inputFields.value.Stadt != "" &&
    inputFields.value.Vorname != "" &&
    inputFields.value.Nachname != "" &&
    emailIsValid.value &&
    acceptTOC.value
  ) {
    return true;
  }
  return false;
};

const confirmOrder = () => {
  order.value["pizzas"] = JSON.parse(JSON.stringify(cart.value));
  order.value["total"] = prices.value["total"];
  order.value["CustomerInfo"] = inputFields.value;

  const date = new Date();
  const currentDate =
    date.getFullYear() +
    "-" +
    date.getMonth() +
    "-" +
    date.getDate() +
    " " +
    date.getHours() +
    ":" +
    date.getMinutes() +
    ":" +
    date.getSeconds();
  order.value["date"] = currentDate;
  sendDataToDatabase(order.value);
  setTimeout(() => {
    closeConfirmationModal();
    goto('/home');
  }, 3000);
};

// Validation section
// Address
const ionAddressInputEl = ref();
const onAddressInput = (ev) => {
  const value = ev.target.value;

  // Removes non alphanumeric characters
  const filteredValue = value.replace(/[^a-zA-Z0-9\s]+/g, "");

  /**
   * Update both the state variable and
   * the component to keep them in sync.
   */
  inputFields.value.Adresse = filteredValue;

  const inputCmp = ionAddressInputEl.value;
  if (inputCmp !== undefined) {
    inputCmp.$el.value = filteredValue;
  }
};

// Postal Code
const ionPostalInputEl = ref();
const validatePostal = (ev) => {
  const value = ev.target.value;

  // Removes non alphabetic characters
  const filteredValue = value.replace(/[^0-9]+/g, "");

  /**
   * Update both the state variable and
   * the component to keep them in sync.
   */
  inputFields.value.Postleitzahl = filteredValue;

  const inputCmp = ionPostalInputEl.value;
  if (inputCmp !== undefined) {
    inputCmp.$el.value = filteredValue;
  }
};

// Firsname, Lastname, City
const ionFirstnameInputEl = ref();
const ionLastnameInputEl = ref();
const ionCityInputEl = ref();
const filterNonAlphaChar = (ev, field, inputEl) => {
  const value = ev.target.value;

  // Removes non alphabetic characters
  const filteredValue = value.replace(/[^a-zA-Z]+/g, "");

  /**
   * Update both the state variable and
   * the component to keep them in sync.
   */
  inputFields.value[field] = filteredValue;

  const inputCmp = inputEl;
  if (inputCmp !== undefined) {
    inputCmp.$el.value = filteredValue;
  }
};

//E-Mail
const emailInputRef = ref(null);

const validateEmail = (email) => {
  return (
    email.match(
      /^(?=.{1,254}$)(?=.{1,64}@)[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z]{2,63})$/
    ) !== null
  );
};

const validate = (ev) => {
  const target = ev.target;
  const value = target.value;

  const inputEl = emailInputRef.value?.$el;
  if (!inputEl) return;

  inputEl.classList.remove("ion-valid", "ion-invalid");

  if (value === "") return;

  if (validateEmail(value)) {
    inputEl.classList.add("ion-valid");
    emailIsValid.value = true;
  } else {
    inputEl.classList.add("ion-invalid");
    emailIsValid.value = false;
  }
};

const markTouched = () => {
  const inputEl = emailInputRef.value?.$el;
  if (inputEl) {
    inputEl.classList.add("ion-touched");
  }
};

// Phonenumber
const phoneOptions = {
  mask: [
    ...Array(4).fill(/\d/),
    " ",
    ...Array(4).fill(/\d/),
    " ",
    ...Array(5).fill(/\d/),
    " ",
  ],
  elementPredicate: (el) => {
    return new Promise((resolve) => {
      requestAnimationFrame(async () => {
        const input = await el.getInputElement();
        resolve(input);
      });
    });
  },
};

onMounted(() => {
  const cartData = localStorage.getItem("cart");
  const pricesData = localStorage.getItem("prices");

  cart.value = cartData ? JSON.parse(cartData) : {};
  prices.value = pricesData ? JSON.parse(pricesData) : { total: 0 };
});
</script>

<style scoped>
#main-layout {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  height: 100%;
}

#payment-information {
  height: 100%;
  flex: 9;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  overflow-y: auto;
}

#payment-list {
  height: 100%;
  padding-left: 16px;
}

#payment-item {
  width: 1400px;
  margin-right: 16px;
}

ion-checkbox {
  margin-left: 42px;
  margin-bottom: 20px;
}

#order-button {
  width: 400px;
  height: 50px;
  font-size: 20px;
  font-weight: bold;
  margin-left: 25px;
}

#shopping-cart {
  flex: 3;
  height: 100%;
  background: #f8f8f8;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

#cart-title {
  text-align: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 10px;
}

#shopping-cart ion-item {
  margin-bottom: 16px;
}

/* Standard Styles */
ion-label {
  font-size: 16px;
}

ion-col {
  display: flex;
  align-items: center;
}

#shopping-cart ion-grid {
  width: 100%;
}

#shopping-cart ion-col {
  margin-right: 16px;
}

#confirmationMessage {
  font-size: 18px;
  color: #333;
  max-width: 80%;
  line-height: 1.5;
}

/* Dark Mode Styles */
@media (prefers-color-scheme: dark) {
  #shopping-cart {
    background-color: #333;
    color: white;
  }

  ion-label {
    color: white;
  }

  .payment-information {
    background-color: #1e1e1e;
  }

  #confirmationMessage {
    color: #F8FAFC;
  }
}

#iconContainer {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 100%;
  text-align: center;
}

#confrimIcon {
  color: greenyellow;
  font-size: 300px;
  margin-bottom: 20px;
}
</style>
