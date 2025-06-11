import 'package:flutter/material.dart';
import 'package:lachaudiere_app/models/evenement.dart';
import 'package:lachaudiere_app/services/api_service.dart';

class EvenementProvider extends ChangeNotifier {
  final ApiService _apiService;

  List<Evenement> _evenements = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<Evenement> get evenements => _evenements;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

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
}