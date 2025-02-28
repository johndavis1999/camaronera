@extends('template.template')
@section('contenido')
    @php
        use Carbon\Carbon;
    @endphp
    <!-- Content Header (Page header) -->
    <section class="content-header">
      {{--  
      <div class="container-fluid">
          <div class="row mb-2">
              <div class="col-sm-6">
                  <h1>Resumen Producciones</h1>
              </div>
              <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                      <li class="breadcrumb-item active">Dashboard</li>
                  </ol>
              </div>
          </div>
      </div>
      --}}
      <div>
        @php
          // Obtener el ID de la primera camaronera
          // Obtener el ID de la camaronera de la solicitud, si existe
          $idCamaroneraRequest = request('camaronera');
          $idCamaroneraDefault = $camaronerasUser ? $camaronerasUser->first()->camaronera->id : null;
    
          // Obtener la fecha de la solicitud, si existe, de lo contrario usar la fecha actual
          $fechaRequest = request('fecha') ? request('fecha') : date('Y-m-d');
          $tablaRequest = request('tabla');
        @endphp
        <form method="get" action="" id="autoSubmitForm">
          <div class="row">
            <div class="form-group col-lg-2">
              <label for="camaronera">Camaronera</label>
              <select class="form-control" id="camaronera" name="camaronera" onchange="document.getElementById('autoSubmitForm').submit()">
                @foreach ($camaronerasUser as $item)
                  <option value="{{ $item->camaronera->id }}"
                      @if($idCamaroneraRequest)
                          {{ $item->camaronera->id == $idCamaroneraRequest ? 'selected' : '' }}
                      @else
                          {{ $item->camaronera->id == $idCamaroneraDefault ? 'selected' : '' }}
                      @endif>
                      {{ $item->camaronera->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-lg-2">
              <label for="camaronera">Tabla BW</label>
              <select class="form-control" id="tabla" name="tabla" onchange="document.getElementById('autoSubmitForm').submit()">
                <option value="">
                  Todas
                </option>
                <option value="ta1" {{ 'ta1' == $tablaRequest ? 'selected' : '' }}>
                  TA1
                </option>
                <option value="ta2" {{ 'ta2' == $tablaRequest ? 'selected' : '' }}>
                  TA2
                </option>
                <option value="ta3" {{ 'ta3' == $tablaRequest ? 'selected' : '' }}>
                  TA3
                </option>
                <option value="ta4" {{ 'ta4' == $tablaRequest ? 'selected' : '' }}>
                  TA4
                </option>
                <option value="ta5" {{ 'ta5' == $tablaRequest ? 'selected' : '' }}>
                  TA5
                </option>
                <option value="ta6" {{ 'ta6' == $tablaRequest ? 'selected' : '' }}>
                  TA6
                </option>
                <option value="ta7" {{ 'ta7' == $tablaRequest ? 'selected' : '' }}>
                  TA7
                </option>
              </select>
            </div>
            <div class="form-group col-lg-2">
              <label for="fecha">Fecha</label>
              <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $fechaRequest }}" onchange="document.getElementById('autoSubmitForm').submit()">
            </div>
          </div>
        </form>        
      </div>
    </section>
    @php
      $producciones_json = json_encode($produccionesItems);
      $items_json = json_encode($items);
    @endphp
    <!-- Botón para generar el PDF -->
    <section class="content">
      @php
          $items = $items;
          $itemAnteriores = $itemAnteriores;
          $proyectoItems = $proyectoItems;
          $produccionesItems = $produccionesItems;
      @endphp

      <!-- Formulario para enviar los datos al controlador -->
      <form id="pdfForm" action="{{ url('/resumen') }}" method="POST" style="display:none;">
          @csrf
          <input type="hidden" name="items" value="{{ json_encode($items) }}">
          <input type="hidden" name="itemAnteriores" value="{{ json_encode($itemAnteriores) }}">
          <input type="hidden" name="proyectoItems" value="{{ json_encode($proyectoItems) }}">
          <input type="hidden" name="produccionesItems" value="{{ json_encode($produccionesItems) }}">
      </form>
      <a class="btn btn-primary" href="#" id="descargarPdf">Descargar PDF</a>
      <div class="row">
        <div class="col-md-2">
          <div id="pesotransf" class=""></div>
        </div>
        <div class="col-md-2">
          <div id="denstransf" class=""></div>
        </div>
        <div class="col-md-2">
          <div id="increm" class=""></div>
        </div>
        <div class="col-md-2">
          <div id="sobrevivencia" class=""></div>
        </div>
        <div class="col-md-2">
          <div id="biomasa" class=""></div>
        </div>
        <div class="col-md-2">
          <div id="densidadprom" class=""></div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body table-responsive p-0" id="miDiv">
              <table class="table table-head-fixed text-nowrap table-bordered" id="grid" >
                <thead>
                  <tr>
                    <th data-type="number" class="bg-secondary">#PS <i class="fas"></i></th>
                    <th data-type="string" class="bg-secondary">Sem. <i class="fas"></i></th>
                    <th data-type="string" class="bg-secondary">Tipo Bal <i class="fas"></i></th>
                    <th data-type="number" class="bg-warning">ha <i class="fas"></i></th>
                    <th data-type="number" class="bg-warning" onclick="sortGrid(3, 'number')">Días <i class="fas"></i></th>
                    <th data-type="number" class="bg-warning">Peso<br>Transf <i class="fas"></i></th>
                    <th data-type="string" class="bg-success">Peso<br>Act <i class="fas"></i></th>
                    <th data-type="string" class="bg-success">Increm <i class="fas"></i></th>
                    <th data-type="string" class="bg-success">Inc. Prom.<br>3sem <i class="fas"></i></th>
                    <th data-type="number" class="bg-success">Kg/ha<br>prom <i class="fas"></i></th>
                    <th data-type="number" class="bg-success">ind/M2 M <i class="fas"></i></th>
                    <th data-type="string" class="bg-success">Alerta <br> Alim <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">Dens<br>bio <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">Dens<br>ADM <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">Pobl. <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">Dens <br> Proy <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">Desvio <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">Lbs/ha <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">lbs/total <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">raleo <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">FCA <i class="fas"></i></th>
                    <th data-type="number" class="bg-primary">FCA <br>Proy <i class="fas"></i></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($items as $item)
                    @php
                      $proyecto = $proyectoItems->where('id_produccion', $item->id_produccion)->where('num_dia', $item->num_dia)->first();
                      $clasePeso = $item->peso_real > $proyecto->peso_proyecto ? 'text-success' : 'text-danger';
                      $iconoPeso = $item->peso_real > $proyecto->peso_proyecto ? 'fas fa-check' : 'fas fa-arrow-up';
                      //$anterior = $itemAnteriores->where('id_produccion', $item->id_produccion)->first();
                    @endphp
                    <tr>
                      <td><a href="{{ url('producciones/'.$item->id_produccion) }}">{{ $item->produccion->piscina->numero }} <i class="fas fa-sign-in-alt"></i></a></td>
                      <td>{{ Carbon::parse($item->fecha)->weekOfYear }}</td>
                      <td>{{ $item->balanceado->nombre }}</td>
                      <td>{{ $item->produccion->piscina->area_ha }}</td>
                      <td class="sortable">{{ $item->num_dia }}</td>
                      <td>{{ $item->produccion->peso_transferencia }}</td>
                      <td class="{{ $clasePeso }}"><i class="{{ $iconoPeso }}"></i> {{ $item->peso_real . '/' . $proyecto->peso_proyecto }}</td>
                      {{-- <td>x</td> --}}
                      <td>{{ $item->peso_real_anterior }}</td>
                      <td class="{{ $item->inc3sem < $item->peso_real_anterior ? 'text-success' : 'text-danger' }}">
                        @if ($item->inc3sem < $item->peso_real_anterior)
                            <span>&uarr;</span> <!-- Flecha hacia arriba -->
                        @elseif ($item->inc3sem > $item->peso_real_anterior)
                            <span>&darr;</span> <!-- Flecha hacia abajo -->
                        @endif
                          {{ number_format($item->inc3sem, 2) }}
                      </td>
                      <td>{{ number_format($item->alimento/$item->produccion->piscina->area_ha, 2) }}</td>
                      <td>{{ $item->densidad_consumo }}</td>
                      <td>
                        @if ($proyecto->alimento_dia != 0)
                          @php
                            $diferencia = (($item->alimento - $proyecto->alimento_dia) / $proyecto->alimento_dia) * 100;
                            $clase = $diferencia < 0 ? 'text-danger' : 'text-success';
                            $icono = $diferencia < 0 ? 'fa-arrow-up' : 'fa-check';
                          @endphp
                          <span class="{{ $clase }}">
                            <i class="fas {{ $icono }}"></i>
                            {{ number_format($diferencia, 2) }}%
                          </span>
                        @else
                          N/A
                        @endif
                      </td>
                      <td>{{ $item->densidad_actual }}</td>
                      <td>{{ $item->densidad_oficina }}</td>
                      <td>{{ $item->densidad_muestreo }}</td>
                      {{-- <td>{{ $proyecto->biomasa }}</td> --}}
                      <!-- aqui se resta con nuevo rpoy d -->
                      <td>{{ $proyecto->densidad }}</td>
                      <td class="{{ $proyecto->densidad - $item->densidad_actual > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($proyecto->densidad - $item->densidad_actual, 2) }}</td>
                      <td>{{ $item->biomasa_actual }}</td>
                      <td>{{ $item->alimento }}</td>
                      <td>{{ $item->densidad_raleada }}</td>
                      <td>{{ $item->fca }}</td>
                      <td>{{ $proyecto->fca }}</td>

                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>
      <!-- /.row -->
    </section>

    <script>
        document.getElementById('descargarPdf').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('pdfForm').submit();
        });
    </script>

    <script>
      const grid = document.getElementById('grid');
  
      grid.addEventListener('click', function(e) {
        if (e.target.tagName !== 'TH') return;
  
        const th = e.target;
        // Si TH, entonces ordena
        sortGrid(th.cellIndex, th.dataset.type, th);
      });
  
      function sortGrid(colNum, type, th) {
        const tbody = grid.querySelector('tbody');
        const rowsArray = Array.from(tbody.rows);
        const isAscending = th.dataset.order === 'asc' || !th.dataset.order; // Determina si el orden es ascendente o descendente
        th.dataset.order = isAscending ? 'desc' : 'asc'; // Alterna la dirección de ordenamiento
  
        // Comparador basado en el tipo de datos
        let compare;
        switch (type) {
          case 'number':
            compare = (rowA, rowB) => (parseFloat(rowA.cells[colNum].innerHTML) - parseFloat(rowB.cells[colNum].innerHTML)) * (isAscending ? 1 : -1);
            break;
          case 'string':
            compare = (rowA, rowB) => rowA.cells[colNum].innerHTML.localeCompare(rowB.cells[colNum].innerHTML) * (isAscending ? 1 : -1);
            break;
        }
  
        // Ordenar las filas
        rowsArray.sort(compare);
  
        // Reinsertar las filas ordenadas en el tbody
        tbody.append(...rowsArray);
  
        // Quitar iconos de todas las cabeceras
        grid.querySelectorAll('th i').forEach(icon => {
          icon.className = 'fas';
          icon.classList.remove('text-primary');
        });
  
        // Agregar el icono de orden en la cabecera actual
        const icon = th.querySelector('i');
        icon.className = isAscending ? 'fas fa-arrow-up text-primary' : 'fas fa-arrow-down text-primary';
      }
    </script>
  

    <!-- Incluir la biblioteca html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <!-- Script para generar el PDF -->
    <script>
      document.getElementById('descargarPdf').addEventListener('click', () => {
          // Seleccionar el div que quieres convertir a PDF
          var elemento = document.getElementById('miDiv');

          // Opciones para html2pdf
          var opciones = {
              margin: 0.1, // Márgenes reducidos a 0.25 pulgadas en todos los lados
              filename: 'documento.pdf',
              image: { type: 'jpeg', quality: 0.98 },
              html2canvas: { scale: 2 },
              jsPDF: { unit: 'in', format: 'A4', orientation: 'landscape' } // Cambiar a orientación horizontal
          };

          // Generar el PDF
          html2pdf().set(opciones).from(elemento).save();
      });
  </script>
  <script>
      // Convertir el JSON PHP a un array de JavaScript
      var producciones = <?php echo $producciones_json; ?>;
      
      // Verificar los datos de producciones
      console.log("Producciones:", producciones);

      // Inicializar variables
      var total = 0;
      var count = 0;

      // Asegurarse de que todos los valores sean números y filtrar valores no numéricos
      producciones = producciones.map(item => {
          return {peso_transferencia: Number(item.peso_transferencia)};
      }).filter(item => !isNaN(item.peso_transferencia));

      // Obtener el valor máximo y mínimo
      var max = Math.max(...producciones.map(item => item.peso_transferencia));
      var min = Math.min(...producciones.map(item => item.peso_transferencia));

      // Calcular el total de los pesos de transferencia
      producciones.forEach(item => {
          total += item.peso_transferencia;
          count++;
      });

      // Calcular el promedio
      var value = total / count;

      // Verificar los valores calculados
      console.log("Max:", max);
      console.log("Min:", min);
      console.log("Promedio:", value);

      // Crear el gráfico JustGage
      var g = new JustGage({
        id: "pesotransf",
        value: value,
        min: min,
        max: max,
        title: "Peso Transferencia Promedio",
        decimals: 2, // Mostrar dos decimales
      });
  </script>
  <script>
      // Convertir el JSON PHP a un array de JavaScript
      var producciones = <?php echo $producciones_json; ?>;
      
      // Verificar los datos de producciones
      console.log("Producciones:", producciones);

      // Inicializar variables
      var total = 0;
      var count = 0;

      // Asegurarse de que todos los valores sean números y filtrar valores no numéricos
      producciones = producciones.map(item => {
          return {densidad: Number(item.densidad)};
      }).filter(item => !isNaN(item.densidad));

      // Obtener el valor máximo y mínimo
      var max = Math.max(...producciones.map(item => item.densidad));
      var min = Math.min(...producciones.map(item => item.densidad));

      // Calcular el total de los pesos de transferencia
      producciones.forEach(item => {
          total += item.densidad;
          count++;
      });

      // Calcular el promedio
      var value = total / count;

      // Verificar los valores calculados
      console.log("Max:", max);
      console.log("Min:", min);
      console.log("Promedio:", value);


      // Crear el gráfico JustGage
      var g = new JustGage({
        id: "denstransf",
        value: value,
        min: min,
        max: max,
        title: "Densidad Transferencia Promedio",
        decimals: 2, // Mostrar dos decimales
      });
  </script>
  <script>
      // Convertir el JSON PHP a un array de JavaScript
      var items = <?php echo $items_json; ?>;
      console.log("items", items)
      // Inicializar variables
      var total = 0;
      var count = 0;

      // Asegurarse de que todos los valores sean números y filtrar valores no numéricos
      items = items.map(item => {
          return {peso_real_anterior: Number(item.peso_real_anterior)};
      }).filter(item => !isNaN(item.peso_real_anterior));

      // Obtener el valor máximo y mínimo
      var max = Math.max(...items.map(item => item.peso_real_anterior));
      var min = Math.min(...items.map(item => item.peso_real_anterior));

      // Calcular el total de los pesos de transferencia
      items.forEach(item => {
          total += item.peso_real_anterior;
          count++;
      });

      // Calcular el promedio
      var value = total / count;

      // Verificar los valores calculados
      console.log("Max:", max);
      console.log("Min:", min);
      console.log("Promedio:", value);


      // Crear el gráfico JustGage
      var g = new JustGage({
        id: "increm",
        value: value,
        min: min,
        max: max,
        title: "Prom. Increm.",
        decimals: 2, // Mostrar dos decimales
      });
  </script>
  <script>
      // Convertir el JSON PHP a un array de JavaScript
      var items = <?php echo $items_json; ?>;
      console.log("items", items)
      // Inicializar variables
      var total = 0;
      var count = 0;

      // Asegurarse de que todos los valores sean números y filtrar valores no numéricos
      items = items.map(item => {
          return {supervivencia: Number(item.supervivencia)};
      }).filter(item => !isNaN(item.supervivencia));

      // Obtener el valor máximo y mínimo
      var max = Math.max(...items.map(item => item.supervivencia));
      var min = Math.min(...items.map(item => item.supervivencia));

      // Calcular el total de los pesos de transferencia
      items.forEach(item => {
          total += item.supervivencia;
          count++;
      });

      // Calcular el promedio
      var value = total / count;

      // Verificar los valores calculados
      console.log("Max:", max);
      console.log("Min:", min);
      console.log("Promedio:", value);


      // Crear el gráfico JustGage
      var g = new JustGage({
        id: "sobrevivencia",
        value: value,
        min: min,
        max: max,
        title: "Suprv Prom,",
        decimals: 2, // Mostrar dos decimales
      });
  </script>
  <script>
      // Convertir el JSON PHP a un array de JavaScript
      var items = <?php echo $items_json; ?>;
      console.log("items", items)
      // Inicializar variables
      var total = 0;
      var count = 0;

      // Asegurarse de que todos los valores sean números y filtrar valores no numéricos
      items = items.map(item => {
          return {biomasa_actual: Number(item.biomasa_actual)};
      }).filter(item => !isNaN(item.biomasa_actual));

      // Obtener el valor máximo y mínimo
      var max = Math.max(...items.map(item => item.biomasa_actual));
      var min = Math.min(...items.map(item => item.biomasa_actual));

      // Calcular el total de los pesos de transferencia
      items.forEach(item => {
          total += item.biomasa_actual;
          count++;
      });

      // Calcular el promedio
      var value = total / count;

      // Verificar los valores calculados
      console.log("Max:", max);
      console.log("Min:", min);
      console.log("Promedio:", value);


      // Crear el gráfico JustGage
      var g = new JustGage({
        id: "biomasa",
        value: value,
        min: min,
        max: max,
        title: "BM Prom,",
        decimals: 2, // Mostrar dos decimales
      });
  </script>
  <script>
    // Convertir el JSON PHP a un array de JavaScript
    var items = <?php echo $items_json; ?>;
    console.log("items", items)
    // Inicializar variables
    var total = 0;
    var count = 0;

    // Asegurarse de que todos los valores sean números y filtrar valores no numéricos
    items = items.map(item => {
        return {densidad_actual: Number(item.densidad_actual)};
    }).filter(item => !isNaN(item.densidad_actual));

    // Obtener el valor máximo y mínimo
    var max = Math.max(...items.map(item => item.densidad_actual));
    var min = Math.min(...items.map(item => item.densidad_actual));

    // Calcular el total de los pesos de transferencia
    items.forEach(item => {
        total += item.densidad_actual;
        count++;
    });

    // Calcular el promedio
    var value = total / count;

    // Verificar los valores calculados
    console.log("Max:", max);
    console.log("Min:", min);
    console.log("Promedio:", value);


    // Crear el gráfico JustGage
    var g = new JustGage({
      id: "densidadprom",
      value: value,
      min: min,
      max: max,
      title: "Dens Prom,",
      decimals: 2, // Mostrar dos decimales
    });
  </script>
  
@endsection