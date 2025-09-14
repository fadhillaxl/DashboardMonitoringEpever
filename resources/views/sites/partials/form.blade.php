<div class="mb-3">
    <label class="form-label fw-bold" for="nama_site">Nama Site <span class="text-danger">*</span></label>
    <input type="text" 
           id="nama_site" 
           name="nama_site" 
           class="form-control @error('nama_site') is-invalid @enderror" 
           value="{{ old('nama_site', $site->nama_site ?? '') }}" 
           required>
    @error('nama_site')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-bold" for="alamat_lengkap">Alamat Lengkap</label>
    <textarea id="alamat_lengkap" 
              name="alamat_lengkap" 
              class="form-control @error('alamat_lengkap') is-invalid @enderror" 
              rows="3">{{ old('alamat_lengkap', $site->alamat_lengkap ?? '') }}</textarea>
    @error('alamat_lengkap')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-bold" for="lokasi">Lokasi <span class="text-danger">*</span></label>
    <input type="text" 
           id="lokasi" 
           name="lokasi" 
           class="form-control @error('lokasi') is-invalid @enderror" 
           value="{{ old('lokasi', $site->lokasi ?? '') }}" 
           required>
    @error('lokasi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-bold" for="pic">PIC</label>
    <input type="text" 
           id="pic" 
           name="pic" 
           class="form-control @error('pic') is-invalid @enderror" 
           value="{{ old('pic', $site->pic ?? '') }}">
    @error('pic')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-bold" for="mac_address">MAC Address <span class="text-danger">*</span></label>
    <input type="text" 
           id="mac_address" 
           name="mac_address" 
           class="form-control @error('mac_address') is-invalid @enderror" 
           value="{{ old('mac_address', $site->mac_address ?? '') }}" 
           required>
    @error('mac_address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
