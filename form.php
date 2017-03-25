<?php include ("header.php");?>


    <div class="container theme-showcase" role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1>Forms </h1>
        <p>Use the code below to create forms.</p>
        <p>Forms can also be build on <a href="http://bootsnipp.com/forms">http://bootsnipp.com/forms</a></p>
      </div>





      <div class="page-header">
        <h1>Alerts</h1>
      </div>
      <div class="alert alert-success" role="alert">
        Forms can be put in a green box
      </div>
      <div class="alert alert-info" role="alert">
        Forms can be put in a green box
      </div>
      <div class="alert alert-warning" role="alert">
        Forms can be put in a green box
      </div>




      <div class="page-header">
        <h1>Panels</h1>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Form title</h3>
            </div>
            <div class="panel-body">
              Form can go here
            </div>
          </div>
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">Form title</h3>
            </div>
            <div class="panel-body">
              Form can go here
            </div>
          </div>
        </div><!-- /.col-sm-4 -->
        <div class="col-sm-4">
          <div class="panel panel-success">
            <div class="panel-heading">
              <h3 class="panel-title">Form title</h3>
            </div>
            <div class="panel-body">
              Form can go here
            </div>
          </div>
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Form title</h3>
            </div>
            <div class="panel-body">
              Form can go here
            </div>
          </div>
        </div><!-- /.col-sm-4 -->
        <div class="col-sm-4">
          <div class="panel panel-warning">
            <div class="panel-heading">
              <h3 class="panel-title">Form title</h3>
            </div>
            <div class="panel-body">
              Form can go here
            </div>
          </div>
          <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">Form title</h3>
            </div>
            <div class="panel-body">
              Form can go here
            </div>
          </div>
        </div><!-- /.col-sm-4 -->
      </div>


      <div class="page-header">
        <h1>Wells</h1>
      </div>
      <div class="well">
        <form class="form-horizontal">
<fieldset>

<!-- Form Name -->
<legend>Primechain Form</legend>

<!-- Button (Double) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="button1id">Double Button</label>
  <div class="col-md-8">
    <button id="button1id" name="button1id" class="btn btn-success">Good Button</button>
    <button id="button2id" name="button2id" class="btn btn-danger">Scary Button</button>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton">Single Button</label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Button</button>
  </div>
</div>

<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="filebutton">File Button</label>
  <div class="col-md-4">
    <input id="filebutton" name="filebutton" class="input-file" type="file">
  </div>
</div>

<!-- Select Multiple -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectmultiple">Select Multiple</label>
  <div class="col-md-4">
    <select id="selectmultiple" name="selectmultiple" class="form-control" multiple="multiple">
      <option value="1">Option one</option>
      <option value="2">Option two</option>
    </select>
  </div>
</div>

<!-- Multiple Radios (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="radios">Inline Radios</label>
  <div class="col-md-4"> 
    <label class="radio-inline" for="radios-0">
      <input type="radio" name="radios" id="radios-0" value="1" checked="checked">
      1
    </label> 
    <label class="radio-inline" for="radios-1">
      <input type="radio" name="radios" id="radios-1" value="2">
      2
    </label> 
    <label class="radio-inline" for="radios-2">
      <input type="radio" name="radios" id="radios-2" value="3">
      3
    </label> 
    <label class="radio-inline" for="radios-3">
      <input type="radio" name="radios" id="radios-3" value="4">
      4
    </label>
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">Select Basic</label>
  <div class="col-md-4">
    <select id="selectbasic" name="selectbasic" class="form-control">
      <option value="1">Option one</option>
      <option value="2">Option two</option>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Text Input</label>  
  <div class="col-md-4">
  <input id="textinput" name="textinput" type="text" placeholder="placeholder" class="form-control input-md">
  <span class="help-block">help</span>  
  </div>
</div>

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Inline Checkboxes</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="checkboxes" id="checkboxes-0" value="1">
      1
    </label>
    <label class="checkbox-inline" for="checkboxes-1">
      <input type="checkbox" name="checkboxes" id="checkboxes-1" value="2">
      2
    </label>
    <label class="checkbox-inline" for="checkboxes-2">
      <input type="checkbox" name="checkboxes" id="checkboxes-2" value="3">
      3
    </label>
    <label class="checkbox-inline" for="checkboxes-3">
      <input type="checkbox" name="checkboxes" id="checkboxes-3" value="4">
      4
    </label>
  </div>
</div>

<!-- Multiple Checkboxes -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Multiple Checkboxes</label>
  <div class="col-md-4">
  <div class="checkbox">
    <label for="checkboxes-0">
      <input type="checkbox" name="checkboxes" id="checkboxes-0" value="1">
      Option one
    </label>
	</div>
  <div class="checkbox">
    <label for="checkboxes-1">
      <input type="checkbox" name="checkboxes" id="checkboxes-1" value="2">
      Option two
    </label>
	</div>
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordinput">Password Input</label>
  <div class="col-md-4">
    <input id="passwordinput" name="passwordinput" type="password" placeholder="placeholder" class="form-control input-md">
    <span class="help-block">help</span>
  </div>
</div>

<!-- Multiple Radios -->
<div class="form-group">
  <label class="col-md-4 control-label" for="radios">Multiple Radios</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="radios-0">
      <input type="radio" name="radios" id="radios-0" value="1" checked="checked">
      Option one
    </label>
	</div>
  <div class="radio">
    <label for="radios-1">
      <input type="radio" name="radios" id="radios-1" value="2">
      Option two
    </label>
	</div>
  </div>
</div>

<!-- Textarea -->
<div class="form-group">
  <label class="col-md-4 control-label" for="textarea">Text Area</label>
  <div class="col-md-4">                     
    <textarea class="form-control" id="textarea" name="textarea">default text</textarea>
  </div>
</div>

<!-- Prepended checkbox -->
<div class="form-group">
  <label class="col-md-4 control-label" for="prependedcheckbox">Prepended Checkbox</label>
  <div class="col-md-4">
    <div class="input-group">
      <span class="input-group-addon">     
          <input type="checkbox">     
      </span>
      <input id="prependedcheckbox" name="prependedcheckbox" class="form-control" type="text" placeholder="placeholder">
    </div>
    <p class="help-block">help</p>
  </div>
</div>

</fieldset>
</form>

      </div>




    </div> <!-- /container -->
    <?php include ("footer.php");?>