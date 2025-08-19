<template>


    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        <slot name="BreadCrumbs"></slot>
                    </div>
                    <h2 class="page-title">
                        <slot name="PageTitle"></slot>
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <slot name="RightButtons"></slot>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <slot />
        </div>
    </div>



</template>

<script>

export default {



}

</script>
