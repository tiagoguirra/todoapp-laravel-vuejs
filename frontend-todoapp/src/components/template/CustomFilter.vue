<template>
  <div class="bp-dropdown" :class="{ className, 'bp-dropdown--sub': role }">
    <span
      :class="{ [`bp-dropdown__${(role) ? 'sub' : 'btn'}`]: true, [`bp-dropdown__${(role) ? 'sub' : 'btn'}--active`]: !isHidden, [`${className}-bp__btn`]: className, [`${className}-bp__btn--active`]: !isHidden }"
      @click="_onToggle"
      @mouseenter="_onBtnEnter"
      @mouseleave="_onBtnLeave"
    >
      <slot name="btn"></slot>
      <slot name="icon" v-if="isIcon">
        <svg
          v-if="isLoading"
          class="bp-dropdown__icon bp-dropdown__icon--spin"
          viewBox="0 0 512 512"
        >
          <path
            fill="currentColor"
            d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"
          ></path>
        </svg>
        <svg
          v-else
          class="bp-dropdown__icon"
          :class="{ [`bp-dropdown__icon--${align}`]: align }"
          viewBox="0 0 256 512"
        >
          <path
            fill="currentColor"
            d="M119.5 326.9L3.5 209.1c-4.7-4.7-4.7-12.3 0-17l7.1-7.1c4.7-4.7 12.3-4.7 17 0L128 287.3l100.4-102.2c4.7-4.7 12.3-4.7 17 0l7.1 7.1c4.7 4.7 4.7 12.3 0 17L136.5 327c-4.7 4.6-12.3 4.6-17-.1z"
          ></path>
        </svg>
      </slot>
    </span>
    <transition name="fade">
      <div
        v-if="!isHidden"
        class="bp-dropdown__body"
        :id="id"
        :style="{ minWidth: `${width}px`, top: `${top}px`, left: `${left}px` }"
        :class="{ [`${className}-bp__body`]: className }"
        @click="_onBodyClick"
        @mouseenter="_onBodyEnter"
        @mouseleave="_onBodyLeave"
      >
        <slot name="body"></slot>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: "custom-filter",
  props: {
    role: {
      type: String,
      required: false,
      default: ""
    },
    unscroll: {
      type: [HTMLElement, String],
      required: false,
      default: null
    },
    align: {
      type: String,
      required: false,
      default: "bottom"
    },
    x: {
      type: Number,
      required: false,
      default: 0
    },
    y: {
      type: Number,
      required: false,
      default: 0
    },
    beforeOpen: {
      type: Function,
      required: false,
      default: resolve => resolve()
    },
    trigger: {
      type: String,
      required: false,
      default: "click"
    },
    closeOnClick: {
      type: Boolean,
      required: false,
      default: false
    },
    isIcon: {
      type: Boolean,
      required: false,
      default: true
    },
    className: {
      type: String,
      required: false,
      default: ""
    }
  },
  data: () => ({
    isHidden: true,
    isLoading: false,
    id: null,
    timeout: null,
    top: undefined,
    right: undefined,
    bottom: undefined,
    left: undefined,
    width: undefined
  }),
  watch: {
    isHidden(isHidden) {
      if (this.unscroll) {
        const el =
          this.unscroll instanceof HTMLElement
            ? this.unscroll
            : document.querySelector(this.unscroll);
        if (el) {
          el.style.overflow = !isHidden ? "hidden" : "";
        }
      }
    }
  },
  created() {
    const $root = this.$root;
    // --- hide dropdown if other dropdowns show
    // --- or document clicked
    $root.$on("bp-dropdown:open", () => (this.isHidden = true));
    $root.$on("bp-dropdown:hide", () => (this.isHidden = true));
    // --- hide dropdown on document click event
    if (this.trigger === "click" && !$root["is-bp-dropdown"]) {
      Object.defineProperty($root, "is-bp-dropdown", {
        enumerable: false,
        configurable: false,
        writable: false,
        value: true
      });
      document.onmousedown = e => {
        const target = e.target;
        const dropdown =
          target.closest(".bp-dropdown__btn") ||
          target.closest(".bp-dropdown__body");
        if (!dropdown) {
          $root.$emit("bp-dropdown:hide");
        }
      };
    }
    this.id = "bp-dropdown-" + this.generateRandomId();
  },
  methods: {
    // --- generate random id for query selector
    generateRandomId() {
      return Math.random()
        .toString(36)
        .substr(2, 10);
    },
    _onToggle(e) {
      if (this.trigger !== "click") {
        return;
      }
      this.checkCustomCallback(e);
    },
    _onBtnEnter(e) {
      if (this.trigger !== "hover" || !this.isHidden) {
        return;
      }
      this.checkCustomCallback(e);
    },
    _onBtnLeave(e) {
      if (this.trigger !== "hover") {
        return;
      }
      if (this.role) {
        this.timeout = setTimeout(() => (this.isHidden = true), 100);
      }
      const to = e.toElement;
      if (!to) {
        return;
      }
      const isDropdown =
        to.closest(".bp-dropdown__btn") || to.closest(".bp-dropdown__body");
      if (isDropdown) {
        return;
      }
      this.prepare();
    },
    _onBodyClick() {
      if (this.closeOnClick) {
        this.isHidden = true;
      }
    },
    _onBodyEnter() {
      if (this.timeout) {
        clearTimeout(this.timeout);
      }
    },
    _onBodyLeave(e) {
      if (this.trigger !== "hover") {
        return;
      }
      const to = e.toElement;
      if (!to) {
        return;
      }
      if (to.closest(".bp-dropdown__btn") || to.closest(".bp-dropdown__sub")) {
        return;
      }
      this.prepare();
    },
    checkCustomCallback(e) {
      if (!this.isHidden) {
        this.prepare();
        return;
      }
      // --- custom callback before open
      const promise = new Promise(resolve => {
        this.isLoading = true;
        this.beforeOpen.call(this, resolve);
      });
      promise.then(() => {
        this.isLoading = false;
        if (!e.target.closest(".bp-dropdown__body")) {
          // --- hide dropdown if other dropdowns show
          this.$root.$emit("bp-dropdown:open");
        }
        setTimeout(this.prepare, 0);
      });
      promise.catch(() => {
        throw Error("bp-dropdown promise error");
      });
    },
    prepare() {
      this.isHidden = !this.isHidden;
      if (!this.isHidden) {
        this.$nextTick(() => {
          const button = this.$el.firstElementChild;
          const container = document.getElementById(this.id);
          this.setWidth(button.offsetWidth);
          // this.setPosition(button, container);
        });
      }
    },
    setWidth(width) {
      this.width = width;
    },

    getCoords(el) {
      el = el.getBoundingClientRect();
      return {
        top: el.top - pageYOffset,
        left: el.left - pageXOffset
      };
    }
  }
};
</script>

<style>
.bp-dropdown {
  width: 100%;
  position: relative;
}
.bp-dropdown--sub {
  width: 100%;
}
.bp-dropdown--sub .bp-dropdown__btn,
.bp-dropdown--sub .bp-dropdown__sub {
  width: 100%;
}
.bp-dropdown--sub .bp-dropdown__icon {
  margin-left: auto;
}
.bp-dropdown__btn {
  display: inline-flex;
  align-items: center;
  padding: 3px 25px 3px 10px;
  border: 1px solid #e9e9e9;
  cursor: pointer;
  height: 45px;  
  width: 100%;
  font-size: 0.8rem;
  font-weight: 300;
  transition: background-color 0.1s ease;
  position: relative;
  background: #fff;
}
.bp-dropdown__sub {
  display: inline-flex;
  align-items: center;
}
.bp-dropdown__btn--active {
  background-color: #eee;
}
.bp-dropdown__icon {
  display: inline-block;
  width: 22px;
  height: 22px;
  overflow: visible;
  transition: transform 0.1s ease;
  position: absolute;
  right: 5px;
}
.bp-dropdown__icon--spin {
  width: 12px;
  height: 12px;
  animation: spin 2s infinite linear;
}
.bp-dropdown__icon--top {
  transform: rotate(-180deg);
}
.bp-dropdown__icon--right {
  transform: rotate(-90deg);
}
.bp-dropdown__icon--bottom {
  transform: rotate(0);
  margin-left: 10px;
}
.bp-dropdown__icon--left {
  transform: rotate(-270deg);
}
.bp-dropdown__btn--active .bp-dropdown__icon--top,
.bp-dropdown__sub--active .bp-dropdown__icon--top {
  transform: rotate(0);
}
.bp-dropdown__btn--active .bp-dropdown__icon--right,
.bp-dropdown__sub--active .bp-dropdown__icon--right {
  transform: rotate(-270deg);
}
.bp-dropdown__btn--active .bp-dropdown__icon--bottom,
.bp-dropdown__sub--active .bp-dropdown__icon--bottom {
  transform: rotate(-180deg);
  margin-left: 10px;
}
.bp-dropdown__btn--active .bp-dropdown__icon--left,
.bp-dropdown__sub--active .bp-dropdown__icon--left {
  transform: rotate(-90deg);
}
.bp-dropdown__body {
  position: absolute;
  top: 40%;
  left: 5%;
  /* padding: 6px 8px; */
  background-color: #fff;
  box-shadow: 0 5px 15px -5px rgba(0, 0, 0, 0.5);
  z-index: 9999;
}
.bp-dropdown__body ul li {
  padding: 15px 0;
  text-align: center;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 300;
}
.bp-dropdown__body ul li:hover {
  background-color: #eee;
}
.bp-dropdown__body ul li.active {
  background-color: #eee;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
@keyframes spin {
  0% {
    transform: rotate(0);
  }
  100% {
    transform: rotate(360deg);
  }
}
@media (max-width: 720px) {
  .bp-dropdown,.bp-dropdown__btn{
    width: 100%;
  }
  .bp-dropdown__btn{    
    margin-left: inherit!important;    
    margin-top: 5px;
    border: 1px solid #e9e9e9;    
    height: 50px;
  }  
}
@media (min-width: 720px) {
  .bp-dropdown__btn {
    /* border-left: none; */    
  }
}
</style>
