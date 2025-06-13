import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:lachaudiere_app/providers/evenement_provider.dart';
import 'package:lachaudiere_app/providers/theme_provider.dart'; // <-- Ajout
import 'package:lachaudiere_app/screens/evenements_master.dart';
import 'package:lachaudiere_app/services/api_service.dart';

const String _baseUrl = "http://localhost:12345/api";

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('fr_FR', null);

  runApp(const LaChaudiereApp());
}

class LaChaudiereApp extends StatelessWidget {
  const LaChaudiereApp({super.key});

  @override
  Widget build(BuildContext context) {
    final apiService = ApiService(baseUrl: _baseUrl);

    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => ThemeProvider()),
        ChangeNotifierProvider(create: (_) => EvenementProvider(apiService: apiService)),
      ],
      child: Consumer<ThemeProvider>(
        builder: (context, themeProvider, child) {
          return MaterialApp(
            title: 'La Chaudière',
            theme: ThemeData(
              useMaterial3: true,
              colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFFE65100)),
              appBarTheme: const AppBarTheme(
                backgroundColor: Color(0xFFE65100),
                foregroundColor: Colors.white,
              ),
            ),
            darkTheme: ThemeData.dark(),
            themeMode: themeProvider.themeMode,
            home: const EvenementsMaster(),
            debugShowCheckedModeBanner: false,
          );
        },
      ),
    );
  }
}