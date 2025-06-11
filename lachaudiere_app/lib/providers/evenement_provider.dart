import 'package:flutter/material.dart';
import 'package:lachaudiere_app/models/categorie.dart';
import 'package:lachaudiere_app/models/evenement.dart';
import 'package:lachaudiere_app/services/api_service.dart';

class EvenementProvider extends ChangeNotifier {
  final ApiService _apiService;

  List<Evenement> _evenements = [];
  List<Categorie> _categories = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Evenement> get evenements => _evenements;
  List<Categorie> get categories => _categories;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  EvenementProvider({required ApiService apiService}) : _apiService = apiService {
    _initialLoad();
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

  Future<void> refreshData() async {
    await _initialLoad();
  }

  Future<void> fetchAndApplyDetails(Evenement evenement) async {
    try {
      final detailsJson = await _apiService.fetchEvenementDetails(evenement.id);
      evenement.updateWithDetails(detailsJson);
      notifyListeners();
    } catch (e) {
      print("Erreur de chargement des détails pour l'événement ${evenement.id}: $e");
    }
  }
}