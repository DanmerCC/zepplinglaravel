<template>
    <select class="form-select" name="" id="" v-model="int_input">
        <option
            v-for="(option, index) in option_hora"
            :value="option"
            :key="index"
            aria-placeholder="Hora"
        >
            {{ option }}
        </option>
    </select>
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            default: "01",
        },
    },
    watch: {
        int_input(newValue, oldValue) {
            this.$emit("input", newValue);
        },
    },
    mounted() {
        for (let index = 10; index < 61; index++) {
            this.option_hora.push(index.toString());

        }
        this.$emit("input", this.int_input);
    },
    data() {
        return {
            int_input: null,
            /** 60 */
            option_hora: [
                null,
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09"
            ],
        };
    },
};
</script>

<style></style>
