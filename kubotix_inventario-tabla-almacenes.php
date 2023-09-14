<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacenes</title>
</head>
<body>

<div class="content d-flex flex-column flex-column-fluid" id="vue-content" style="padding: 0px; position: relative; top: 5px;">
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container-fluid" v-cloak>
                <!--begin::Card-->
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-1 pb-0" style="padding: 0.5em;">
                        <div class="card-body table-responsive">
                            <!--begin: Table-->
                            <table class="table table-separate table-head-custom table-checkable" id="table-almacenes">
                                <thead>
                                    <tr>
                                        <th>Clave</th>
                                        <th>Almacén</th>
                                        <th>Dirección</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="a in almacenes">
                                        <tr role="row" class="odd">
                                            <td class="dtr-control" tabindex="0" style="">
                                                {{a.CLAVE}}
                                            </td>
                                            <td>{{a.NOMBRE_ALMACEN}}</td>
                                            <td>{{a.DIRECCION_ALMACEN}}</td>
                                            <td>{{a.TIPO_ALMACEN}}</td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <button type="button" class="btn btn-sm btn-default btn-text-primary btn-hover-primary mr-2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                        opciones
                                                    </button>
                                                    <div class="dropdown-menu" aria-labellebdy="dropdownMenuButton"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <!--end: Table-->
                            <div class="col-12">
                                <!--begin::Pagination-->
                                <div class="d-flex justify-content-between align-items-center flex-wrap" style="float: right;">
                                    <div class="d-flex flex-wrap py-2 mr-3">
                                        <a @click="mover_pagina(1)" v-bind:class="(pagina_actual==1)?'disabled':''" class="btn btn-icon btn-sm btn-light mr-2 my-1">
                                            <i class="ki ki-bold-double-arrow-back icon-xs"></i>
                                        </a>
                                        <a @click="mover_pagina(pagina_actual-1)" v-bind:class="(pagina_actual==1)?'disabled':''"  class="btn btn-sm btn-light mr-2 my-1">
                                            <i class="ki ki-bold-arrow-back icon-xs"></i> anterior
                                        </a>

                                        <span class="text-muted ml-20 mr-20 mt-3">Página {{pagina_actual}} de {{total_de_paginas}}</span>

                                        <a @click="mover_pagina(pagina_actual+1)" v-bind:class="(pagina_actual==total_de_paginas)?'disabled':''"  class="btn btn-sm btn-light mr-2 my-1">
                                            siguiente  <i class="ki ki-bold-arrow-next icon-xs"></i>
                                        </a>
                                        <a @click="mover_pagina(total_de_paginas)" v-bind:class="(pagina_actual==total_de_paginas)?'disabled':''"  class="btn btn-icon btn-sm btn-light mr-2 my-1">
                                            <i class="ki ki-bold-double-arrow-next icon-xs"></i>
                                        </a>
                                    </div>
                                </div>
                                <!--end:: Pagination-->
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Entry-->
        </div>
    </div>
</div>

<div class="modal fade" id="vue-modal-insertar-editar" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h5 class="modal-title" id="modal-titulo">Agregar almacén</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Nombre del almacén<span class="text-danger">*</span>
                        </label>
                        <input type="text" v-model="NOMBRE_ALMACEN" class="form-control" placeholder="e.j. CEDIS Norte">
                    </div>
                    <div class="form-group">
                        <label>
                            Clave del almacén<span class="text-danger">*</span>
                        </label>
                        <input type="text" v-model="CLAVE" @input="valida_clave" class="form-control" placeholder="e.j. CDN">
                        <span class="form-text text-muted">
                            Máximo 3 carácteres.
                        </span>
                    </div>
                    <div class="form-group">
                        <label>
                            Dirección del almacén<span class="text-danger">*</span>
                        </label>
                        <input type="text" v-model="DIRECCION_ALMACEN" class="form-control" placeholder="e.j. Av Nacional No. 3 Col. Vistalinda">
                    </div>
                    <div class="form-group">
                        <label>
                            Tipo de almacén
                        </label>
                        <select v-model="TIPO_ALMACEN" class="form-control">
                            <option value="bodega" selected="selected">bodega</option>
                            <option value="sucursal">sucursal</option>
                        </select>
                        <span class="form-text text-muted">
                            Se define cómo "sucursal" cuando es un punto de venta y se define como "bodega" en caso contrario.
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cerrar</button>
                    <button @click="guardar()" type="button" class="btn btn-primary font-weight-bold">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="subheader py-2 py-lg-4 subheader-solid" id="vue-search" v-cloak>
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-item-center flex-wrap mr-2">
                <h6 class="text-dark mt-2 mb-2 mr-5">Almacenes</h6>
                <div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-2 bg-gray-200"></div>
                <span class="text-dark-50 font-weight-bold ml-5" id="kt_subheader_total">
                    {{total_de_resultados}} resultados
                </span>
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <a @click="show_modal_insertar()" class="btn btn-light-primary font-weight-bold btn-sm px-4 font-size-base
                ml-2" data-toogle="tooltip" data-placement="top" data-original-title="Agregar almacén"> Agregar almacén </a>
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
</div>

</body>
</html>