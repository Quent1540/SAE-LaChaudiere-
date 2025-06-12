import 'package:flutter/material.dart';
import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/models/evenement.dart';
import 'package:lachaudiere_app/services/api_service.dart';

class EvenementProvider extends ChangeNotifier {
  final ApiService _apiService;
  String _searchQuery = '';

  List<Evenement> _evenements = [];
  List<Categorie> _categories = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Categorie> get categories => _categories;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;


  List<Evenement> get evenements {
    if (_searchQuery.isEmpty) return _evenements;
    return _evenements
        .where((e) => e.titre.toLowerCase().contains(_searchQuery.toLowerCase()))
        .toList();
  }

  EvenementProvider({required ApiService apiService}) : _apiService = apiService {
    fetchEvenements();
  }

  Future<void> fetchEvenements() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _evenements = await _apiService.fetchEvenements();
      _evenements.sort((a, b) => a.titre.toLowerCase().compareTo(b.titre.toLowerCase()));
    } catch (error) {
      _errorMessage = "Erreur de chargement : $error";
      _evenements = [];
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void updateSearchQuery(String query) {
    _searchQuery = query;
    notifyListeners();
  }

  Future<void> refreshData() async {
    await fetchEvenements();
    await _initialLoad();
  }
  
  Future<void> _initialLoad() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final results = await Future.wait([
        _apiService.fetchEvenements(),
        _apiService.fetchCategories(),
      ]);
      _evenements = results[0] as List<Evenement>;
      _categories = results[1] as List<Categorie>;
      _evenements.sort((a, b) => a.titre.toLowerCase().compareTo(b.titre.toLowerCase()));
    } catch (error) {
      _errorMessage = "Erreur de chargement : $error";
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchAndApplyDetails(Evenement evenement) async {
    try {
      final detailsJson = await _apiService.fetchEvenementDetails(evenement.id);
      
      final domainUrl = _apiService.domainUrl;
      evenement.updateWithDetails(detailsJson, domainUrl);
      
      notifyListeners();
    } catch (e) {
      print("Erreur de chargement des détails pour l'événement ${evenement.id}: $e");
    }
  }
}

