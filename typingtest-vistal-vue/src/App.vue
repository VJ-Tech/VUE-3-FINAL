<script setup>
import { RouterLink, RouterView } from 'vue-router';
import { ref, onMounted, watch, computed } from 'vue';
  
const quoteApiUrl = "http://localhost:8000/api/easy/random?minLength=80&maxLength=100";
let quote = "";
let time = 60;
let timer = ref(time);
let mistakes = ref(0);
const quoteChars = ref([]);

const currentInput = ref("");
const testStarted = ref(false);
const testCompleted = ref(false);
const charactersTyped = ref(0);
const inputRef = ref(null);

const wpm = ref(0);
const accuracy = ref(0);
let intervalId;

onMounted(() => {
  renderNewQuote();
});

const renderNewQuote = async () => {
  const response = await fetch(quoteApiUrl);
  const data = await response.json();
  quote = data.content;
  quoteChars.value = quote.split("").map(value => ({ value, success: false, fail: false }));
};

const startTest = () => {
  mistakes.value = 0;
  testCompleted.value = false;
  testStarted.value = true;
  timeReduce();
};

const checkInput = () => {
  if (!testStarted.value) {
    startTest();
  }

  quoteChars.value.forEach((char, index) => {
    if (currentInput.value[index] === char.value) {
      char.success = true;
      char.fail = false;
    } else if (!currentInput.value[index]) {
      char.success = false;
      char.fail = false;
    } else {
      char.success = false;
      if (!char.fail) {
        mistakes.value += 1;
        char.fail = true;
      }
    }
  });

  charactersTyped.value = currentInput.value.length;

  if (quoteChars.value.every(char => char.success)) {
    displayResult();
  }

  calculateMetrics();
};

const updateTimer = () => {
  if (timer.value === 0) {
    displayResult();
  } else {
    timer.value--;
  }
};

const timeReduce = () => {
  timer.value = time;
  setInterval(updateTimer, 1000);
};

const displayResult = () => {
  testCompleted.value = true;
  clearInterval(intervalId);
  calculateMetrics();
  var par = document.getElementById('paragraph');
  par.classList.add('paragraph');
  
};

const calculateMetrics = () => {
  const timeTaken = (60 - timer.value) / 100;
  const wordsTyped = (charactersTyped.value / 5)- (mistakes.value / 5);
  const wordsPerMinute = wordsTyped / timeTaken;

  if (!isFinite(wordsPerMinute) || wordsPerMinute < 0) {
    wpm.value = "0"; 
  } else {
    wpm.value = Math.round(wordsPerMinute).toString();
  }

  const accuracyPercentage = charactersTyped.value !== 0 ?
    ((charactersTyped.value - mistakes.value) / charactersTyped.value) * 100 :
    100; 

  if (!isFinite(accuracyPercentage) || accuracyPercentage < 0) {
    accuracy.value = "0"; 
  } else {
    accuracy.value = Math.round(accuracyPercentage).toString();
  };
};


watch(charactersTyped, () => {
  if (testStarted.value && !testCompleted.value) {
    calculateMetrics();
  }
});

document.addEventListener('click', function (event) {
  if (event.target.id !== 'myInput') {
    document.getElementById('myInput').focus();
  }
});

</script>

<template>
  <div id="home">
    <div id="upper">
      <div id="menu">
      </div>

      <div id="graph1">
        <p id="wpmscore">{{ wpm }}</p>
        <p class="scorelabel">wpm</p>
      </div>

      <div id="graph2">
        <p id="accuracyscore">{{ accuracy }}%</p>
        <p>accuracy</p>
      </div>

      <div id="graph3">
        <p id="mtk">mistakes</p>
        <p id="mistk" class="letters">{{ mistakes }}</p>
        <hr>
        <p class="letters">{{ charactersTyped }}</p>
        <p id="chr">total characters</p>
      </div>
    </div>

    <div id="middle">
      <input v-model="currentInput" @input="checkInput" ref="inputRef" id="myInput" autofocus/>
      <div id="paragraph">
          <span
              v-for="(char, index) in quoteChars"
              :key="index"
              :class="{ 'success': char.success, 'fail': char.fail }"
            >
              {{ char.value }}
            </span>
        </div>
    </div>

    <div id="bottom">
      <div id="profile">
      </div>

      <div id="custom">
        <div id="restest">
          <a href="/index.html" id="restart"><img src="/src/components/icons/synchronize.png" alt="restart"></a>
          <p>restart test</p>
        </div>
      </div>

      <div id="timer">
        <div v-show="timer > 0">
          <p id="timers">{{ timer }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style>

#home {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: darkslategray;
}

#upper {
  flex: 0 0 30%;
  display: flex;
  flex-direction: row;
}

#middle {
  flex: 0 0 40%;
  position: relative;
  display: flex;
}

#bottom {
  flex: 1;
  display: flex;
  flex-direction: row;
}

#profile {
  flex: 0 0 20%;
}

#custom {
  flex: 1;
}

#timer {
  flex: 0 0 20%;
}

#menu {
  flex: 1;
}

#graph1, #graph2, #graph3 {
  flex: 0 0 10%;
  padding: 5px;
}

#paragraph {
  text-align:center;
}

.paragraph {
  display: none;
}

#restest {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 20px;
}
img {
  width: 50px;
  height: 50px;
}
span {
  color: rgb(176, 176, 176);
  font-size: 30px;
  font-family: monospace;
}

.success {
    color: cyan;
}
  
.fail {
  color: red;
  border-bottom: 2px solid red;
}
  

#myInput {
  padding: 5px;
  font-size: 16px;
  margin-top: 10px;
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;

}

div p {
  margin: 5px 0;
  font-weight: bold;
}

#middle::-webkit-scrollbar {
  display: none;
}

#wpmscore, #accuracyscore {
  text-align: center;
  font-size: 100px;
  font-family: 'Courier New', Courier, monospace;
}

#wpmscore {
  color: lime;
}

#accuracyscore {
  color: gold;
}

.letters {
  font-size: 30px;
  text-align: center;
}

#chr, #mtk {
  text-align: center;
}

#mistk {
  color: orangered;
}

#timers {
  font-size: 100px;
  text-align: center;
  font-family: 'Courier New', Courier, monospace;
  color: aquamarine;
}

#users {
  z-index: 1;
}
</style>