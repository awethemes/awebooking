<style>
	.bd-example {
		padding: 30px 20px;
		background-color: #fff;
		border: solid 1px #eee;
		margin-bottom: 30px;
		border-radius: 3px;
	}

	.bd-example.dark {
		background-color: #1a1a1a;
	}
</style>

<div class="container awebooking-block">
	<div class="bd-example">
		<button type="button" class="button">Default</button>
		<button type="button" class="button button--primary">Primary</button>
		<button type="button" class="button button--link">Link</button>

		<button type="button" class="button button--rounded">Default</button>
		<button type="button" class="button button--rounded button--primary">Primary</button>
	</div>

	<div class="bd-example">
		<div class="form-group">
			<label for="exampleFormControlInput1">Email address</label>
			<input type="email" class="form-input" id="exampleFormControlInput1" placeholder="name@example.com">
		</div>

		<div class="form-group">
			<label for="exampleFormControlSelect1">Example select</label>
			<select class="form-input" id="exampleFormControlSelect1">
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
			</select>
		</div>

		<div class="form-group">
			<label for="exampleFormControlSelect2">Example multiple select</label>
			<select multiple="" class="form-input" id="exampleFormControlSelect2">
				<option>1</option>
				<option>2</option>
				<option>3</option>
				<option>4</option>
				<option>5</option>
			</select>
		</div>

		<div class="form-group">
			<label for="exampleFormControlTextarea1">Example textarea</label>
			<textarea class="form-input" id="exampleFormControlTextarea1" rows="3"></textarea>
		</div>

		<input class="form-input" type="text" placeholder="Readonly input here…" readonly>
		<input class="form-input" type="text" placeholder="Readonly input here…" disabled>
	</div>

	<div class="bd-example">
		<div class="table-container">
			<table class="table">
			<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">First</th>
				<th scope="col">Last</th>
				<th scope="col">Handle</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th scope="row">1</th>
				<td>Mark</td>
				<td>Otto</td>
				<td>@mdo</td>
			</tr>
			<tr>
				<th scope="row">2</th>
				<td>Jacob</td>
				<td>Thornton</td>
				<td>@fat</td>
			</tr>
			<tr>
				<th scope="row">3</th>
				<td>Larry</td>
				<td>the Bird</td>
				<td>@twitter</td>
			</tr>
			</tbody>
		</table>
		</div>
	</div>
</div>
